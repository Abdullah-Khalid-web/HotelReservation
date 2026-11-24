<?php

namespace App\Services\Chatbot;

use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class ConversationState
{
    private const TTL = 86400; // 24 hours

    private function key(int|string $identity): string
    {
        if (is_string($identity) && !str_starts_with($identity, 'guest_')) {
            throw new InvalidArgumentException('Invalid guest identity.');
        }

        return "chatbot:conversation:{$identity}";
    }

    public function get(int|string $identity): array
    {
        return Cache::get($this->key($identity), []);
    }

    public function put(int|string $identity, array $state): void
    {
        Cache::put($this->key($identity), $state, self::TTL);
    }

    public function update(int|string $identity, array $data): array
    {
        $current = $this->get($identity);

        $state = array_replace_recursive($current, $data);

        $this->put($identity, $state);

        return $state;
    }

    public function clear(int|string $identity): void
    {
        Cache::forget($this->key($identity));
    }

    public function touch(int|string $identity): void
    {
        $state = $this->get($identity);

        if (!empty($state)) {
            $this->put($identity, $state);
        }
    }

    public function updateFromTool(
        int|string $identity,
        string $tool,
        array $args,
        mixed $result
    ): void {
        if (!is_array($result) || !($result['success'] ?? false)) {
            return;
        }

        $data = [];

        switch ($tool) {
            case 'book_table':
            case 'create_guest_reservation':

                $reservation = $result['reservation'] ?? [];

                $reservationId = $reservation['id']
                    ?? $result['reservation_id']
                    ?? null;

                if (!$reservationId) {
                    return;
                }

                $data['reservation'] = [
                    'reservation_id' => (int) $reservationId,
                    'hotel_id' => $reservation['hotel_id']
                        ?? $args['hotel_id']
                        ?? null,
                    'guest_name' => $reservation['guest_name']
                        ?? $args['guest_name']
                        ?? null,
                    'date' => $reservation['date']
                        ?? $args['reservation_date']
                        ?? $args['date']
                        ?? null,
                    'time' => $reservation['time']
                        ?? $args['reservation_time']
                        ?? $args['time']
                        ?? null,
                    'guests' => $reservation['guests']
                        ?? $args['number_of_guests']
                        ?? $args['guests']
                        ?? null,
                    'status' => $reservation['status'] ?? null,
                    'confirmation_url' => $result['confirmation_url'] ?? null,
                ];

                // A new reservation must not inherit an old preorder.
                $data['pre_order'] = [
                    'id' => null,
                    'reservation_id' => (int) $reservationId,
                    'items' => [],
                ];

                break;

case 'place_pre_order':
case 'create_guest_pre_order':
case 'manage_pre_order':

    $preOrder = $result['pre_order'] ?? [];

    $data['pre_order'] = [
        'id' => $preOrder['id'] ?? null,
        'reservation_id' => $preOrder['reservation_id']
            ?? $args['reservation_id']
            ?? null,
        'items' => $preOrder['items'] ?? [],
        'subtotal' => $preOrder['subtotal'] ?? null,
        'tax' => $preOrder['tax'] ?? null,
        'total_amount' => $preOrder['total_amount'] ?? null,
    ];

    break;
    
            case 'update_reservation':

                if (!empty($result['reservation'])) {
                    $data['reservation'] = [
                        'reservation_id' => $result['reservation']['id']
                            ?? $result['reservation']['reservation_id']
                            ?? null,
                        ...$result['reservation'],
                    ];
                }

                break;

            case 'cancel_reservation':

                $data['reservation'] = [];
                $data['pre_order'] = [];

                break;
        }

        if (!empty($data)) {
            $this->update($identity, $data);
        }
    }
}
