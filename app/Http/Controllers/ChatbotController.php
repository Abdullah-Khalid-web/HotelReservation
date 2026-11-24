<?php

namespace App\Http\Controllers;

use App\Services\Chatbot\ChatbotPrompt;
use App\Services\Chatbot\ConversationState;
use App\Services\Chatbot\ToolExecutor;
use App\Services\Chatbot\Tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    private const MAX_TOOL_ROUNDS = 3;
    private const MAX_OUTPUT_TOKENS = 400;
    private const MAX_HISTORY = 20;

    public function send(Request $request)
    {
        try {
            return $this->handle($request);
        } catch (\Throwable $e) {
            Log::error('[Chatbot] Unhandled exception', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'reply' => 'I had a small problem. Could you try that once more?',
            ]);
        }
    }

    private function handle(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'history' => ['nullable', 'array', 'max:' . self::MAX_HISTORY],
            'history.*.from' => ['required_with:history', 'in:user,bot'],
            'history.*.text' => ['required_with:history', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        $userId = $user?->id;

        $guestId = null;
        if (!$userId) {
            $guestId = session()->get('chat_guest_id');
            if (!$guestId) {
                $guestId = 'guest_' . Str::random(16);
                session()->put('chat_guest_id', $guestId);
            }
        }

        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            Log::error('Gemini API key missing');
            return response()->json([
                'reply' => 'The assistant is not configured yet. Please contact support.',
            ]);
        }

        $model = config('services.gemini.model', 'gemini-2.0-flash');
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/'
            . $model . ':generateContent?key=' . $apiKey;

        $state = app(ConversationState::class);

        $conversationId = $userId ?: $guestId;

        $conversationState = $state->get($conversationId);

        $contents = $this->buildContents(
            $validated['history'] ?? [],
            $validated['message']
        );

        $prompt = app(ChatbotPrompt::class);
        $systemInstruction = $userId
            ? $prompt->build($userId)
            : $prompt->buildPublic($guestId);

        $executor = app(ToolExecutor::class);

        for ($round = 0; $round < self::MAX_TOOL_ROUNDS; $round++) {

            $payload = [
                'system_instruction' => [
                    'parts' => [['text' => $systemInstruction]],
                ],
                'contents' => $contents,
                'tools' => [
                    ['function_declarations' => Tools::definitions()],
                ],
                'tool_config' => [
                    'function_calling_config' => ['mode' => 'AUTO'],
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'maxOutputTokens' => self::MAX_OUTPUT_TOKENS,
                ],
            ];

            try {
                $response = Http::connectTimeout(5)
                    ->timeout(25)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url, $payload);
            } catch (\Throwable $e) {
                Log::error('Gemini HTTP exception', ['error' => $e->getMessage()]);
                return response()->json([
                    'reply' => 'I had trouble reaching the assistant. Please try again in a moment.',
                ]);
            }

            if (!$response->successful()) {
                Log::error('Gemini API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'reply' => 'The assistant is a little busy. Please try again.',
                ]);
            }

            $data = $response->json();
            $candidate = $data['candidates'][0] ?? null;

            if (!$candidate) {
                Log::error('Gemini returned no candidate', ['response' => $data]);
                return response()->json([
                    'reply' => 'I did not quite catch that. Could you rephrase?',
                ]);
            }

            $parts = $candidate['content']['parts'] ?? [];

            $functionCalls = [];
            $textParts = [];

            foreach ($parts as $part) {
                if (isset($part['functionCall'])) {
                    $functionCalls[] = [
                        'name' => (string) ($part['functionCall']['name'] ?? ''),
                        'args' => $part['functionCall']['args'] ?? [],
                        'thoughtSignature' => $part['thoughtSignature'] ?? null,
                    ];
                    continue;
                }
                if (isset($part['text'])) {
                    $text = trim((string) $part['text']);
                    if ($text !== '')
                        $textParts[] = $text;
                }
            }

            if (empty($functionCalls)) {
                $reply = trim(implode("\n", $textParts));
                if ($reply === '')
                    $reply = 'Could you please rephrase that?';
                $state->touch($conversationId);
                return response()->json(['reply' => $reply]);
            }

            $modelParts = [];
            foreach ($textParts as $text) {
                if (trim($text) !== '')
                    $modelParts[] = ['text' => $text];
            }
            foreach ($functionCalls as $call) {
                $p = [
                    'functionCall' => [
                        'name' => $call['name'],
                        'args' => $this->ensureObject($call['args']),
                    ],
                ];
                if (!empty($call['thoughtSignature'])) {
                    $p['thoughtSignature'] = $call['thoughtSignature'];
                }
                $modelParts[] = $p;
            }

            $contents[] = ['role' => 'model', 'parts' => $modelParts];

            $functionResponses = [];
            foreach ($functionCalls as $call) {
                $name = $call['name'];
                $args = $call['args'] ?? [];

                if (is_object($args)) {
                    $args = json_decode(json_encode($args), true) ?? [];
                }
                if (!is_array($args))
                    $args = [];

                Log::info('[Chatbot Tool]', [
                    'name' => $name,
                    'args' => $args,
                    'user_id' => $userId,
                    'guest' => $guestId,
                ]);

                try {
                    // ✅ FIX: coerce null → 0 for guests
// Always use the active reservation stored in this conversation.
                    if (
                        in_array($name, [
                            'create_guest_pre_order',
                            'place_pre_order',
                            'manage_pre_order',
                        ], true)
                    ) {
                        $savedReservationId = $conversationState['reservation']['reservation_id'] ?? null;

                        if ($savedReservationId) {
                            $args['reservation_id'] = (int) $savedReservationId;
                        }
                    }


                    $result = $executor->execute(
                        $name,
                        $args,
                        $userId ?? 0,
                        $guestId
                    );
                } catch (\Throwable $e) {
                    Log::error('[Chatbot Tool Error]', [
                        'name' => $name,
                        'args' => $args,
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ]);
                    $result = ['success' => false, 'message' => 'Action failed.'];
                }

                Log::info('[Chatbot Tool Result]', [
                    'name' => $name,
                    'result' => $result,
                ]);

                $state->updateFromTool(
                    $conversationId,
                    $name,
                    $args,
                    $result
                );

                // Refresh local state so subsequent tool calls in this request
// also have the latest reservation information.
                $conversationState = $state->get($conversationId);

                $functionResponses[] = [
                    'functionResponse' => [
                        'name' => $name,
                        'response' => $this->ensureObject($result),
                    ],
                ];
            }

            $contents[] = ['role' => 'user', 'parts' => $functionResponses];
        }

        try {
            $finalResponse = Http::connectTimeout(5)
                ->timeout(20)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'system_instruction' => [
                        'parts' => [
                            [
                                'text' => 'Give a short, natural answer based on the previous tool results. Do not call any tools.',
                            ]
                        ],
                    ],
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.2,
                        'maxOutputTokens' => 250,
                    ],
                ]);

            if ($finalResponse->successful()) {
                $finalData = $finalResponse->json();
                $finalParts = $finalData['candidates'][0]['content']['parts'] ?? [];
                $finalText = '';
                foreach ($finalParts as $p) {
                    if (!empty($p['text']))
                        $finalText .= trim($p['text']) . ' ';
                }
                $finalText = trim($finalText);
                if ($finalText !== '') {
                    $state->touch($conversationId);
                    return response()->json(['reply' => $finalText]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[Chatbot] Final summary call failed', ['error' => $e->getMessage()]);
        }

        return response()->json(['reply' => 'Could you please rephrase that?']);
    }

    private function buildContents(array $history, string $message): array
    {
        $contents = [];
        $history = array_slice($history, -self::MAX_HISTORY);

        foreach ($history as $msg) {
            $text = trim((string) ($msg['text'] ?? ''));
            if ($text === '')
                continue;
            $contents[] = [
                'role' => ($msg['from'] ?? 'user') === 'user' ? 'user' : 'model',
                'parts' => [['text' => $text]],
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $message]],
        ];

        return $contents;
    }

    private function ensureObject($data): object
    {
        if (is_object($data))
            return $data;
        if (!is_array($data))
            return (object) ['value' => $data];
        if (empty($data))
            return new \stdClass();
        if (array_is_list($data))
            return (object) ['data' => $data];
        return (object) $data;
    }
}
