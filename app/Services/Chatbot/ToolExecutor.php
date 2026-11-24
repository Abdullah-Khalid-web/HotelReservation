<?php

namespace App\Services\Chatbot;

use App\Models\Hotel;
use App\Models\HotelTable;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ToolExecutor
{
    private function generateGuestToken(string $name): string
{
    $name = Str::slug($name, '');
    $name = ucfirst(substr($name ?: 'Guest', 0, 12));

    do {
        $token = $name . '-' . strtoupper(Str::random(5));
    } while (
        Reservation::where('guest_token', $token)->exists()
    );

    return $token;
}
    public function execute(
        string $name,
        array $args = [],
        int $userId = 0,
        ?string $guestId = null
    ): array {
        try {
            $userOnly = [
                'book_table', 'place_pre_order', 'list_user_reservations',
                'update_reservation', 'cancel_reservation',
            ];

            if ($userId <= 0 && in_array($name, $userOnly, true)) {
                return [
                    'success' => false,
                    'message' => 'That action needs an account. Please register to manage bookings.',
                ];
            }

            return match ($name) {
                // logged-in
                'book_table'             => $this->bookTable($args, $userId),
                'place_pre_order'        => $this->placePreOrder($args, $userId),
                'list_user_reservations' => $this->listUserReservations($args, $userId),
                'update_reservation'     => $this->updateReservation($args, $userId),
                'cancel_reservation'     => $this->cancelReservation($args, $userId),
                'list_hotels'            => $this->listHotels(),
                'manage_pre_order' => $this->managePreOrder(
                    $args,
                    $userId,
                    $guestId
                ),

                // public
                'list_menu'              => $this->listMenu($args),
                'list_available_tables'  => $this->listAvailableTables($args),
                'hotel_info'             => $this->hotelInfo($args),

                // guest
                'create_guest_reservation' => $this->createGuestReservation($args, $guestId),
                'create_guest_pre_order'   => $this->createGuestPreOrder($args, $guestId),

                default => [
                    'success' => false,
                    'message' => "Unknown tool: {$name}",
                ],
            };
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Chatbot tool DB error', [
                'tool'    => $name,
                'args'    => $args,
                'user_id' => $userId,
                'error'   => $e->getMessage(),
                'sql'     => $e->getSql() ?? null,
            ]);
            return [
                'success' => false,
                'message' => 'That could not be saved. Please try once more.',
            ];
        } catch (\Throwable $e) {
            Log::error('Chatbot tool execution failed', [
                'tool'    => $name,
                'args'    => $args,
                'user_id' => $userId,
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return [
                'success' => false,
                'message' => 'I could not complete that request right now.',
            ];
        }
    }

    private function managePreOrder(
    array $args,
    int $userId,
    ?string $guestId
): array {
    $reservationId = (int) ($args['reservation_id'] ?? 0);
    $action = strtolower(trim((string) ($args['action'] ?? '')));
    $productName = trim((string) ($args['product_name'] ?? ''));
    $quantity = max(1, (int) ($args['quantity'] ?? 1));

    $allowedActions = [
        'add',
        'remove',
        'update_quantity',
        'replace',
    ];

    if (!$reservationId) {
        return ['success' => false, 'message' => 'Reservation is required.'];
    }

    if (!in_array($action, $allowedActions, true)) {
        return ['success' => false, 'message' => 'Invalid pre-order action.'];
    }

    /*
    |--------------------------------------------------------------------------
    | Verify reservation ownership
    |--------------------------------------------------------------------------
    */

    if ($userId > 0) {
        $reservation = Reservation::where('id', $reservationId)
            ->where('user_id', $userId)
            ->first();
    } else {
        if (!$guestId) {
            return [
                'success' => false,
                'message' => 'Guest session expired. Please start a new conversation.',
            ];
        }

        $conversation = app(ConversationState::class)->get($guestId);

        $activeReservationId = (int) (
            $conversation['reservation']['reservation_id'] ?? 0
        );

        if ($activeReservationId !== $reservationId) {
            return [
                'success' => false,
                'message' => 'This reservation does not belong to your current session.',
            ];
        }
if (!$guestId) {
    return ['success' => false, 'message' => 'Guest session expired.'];
}

$conversation = app(ConversationState::class)->get($guestId);

$activeReservationId = (int) (
    $conversation['reservation']['reservation_id'] ?? 0
);

if ($activeReservationId !== $reservationId) {
    return ['success' => false, 'message' => 'Reservation access denied.'];
}

$reservation = Reservation::where('id', $reservationId)
    ->whereNull('user_id')
    ->first();
    }

    if (!$reservation) {
        return [
            'success' => false,
            'message' => 'Reservation not found or access denied.',
        ];
    }

    $status = strtolower((string) $reservation->status);

    if (in_array($status, ['cancelled', 'canceled', 'completed'], true)) {
        return [
            'success' => false,
            'message' => "This reservation is {$status}.",
        ];
    }

    if (!Schema::hasTable('pre_orders') ||
        !Schema::hasTable('pre_order_items')) {
        return [
            'success' => false,
            'message' => 'Pre-order system is unavailable.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Get existing order
    |--------------------------------------------------------------------------
    */

    $existingOrder = DB::table('pre_orders')
        ->where('reservation_id', $reservationId)
        ->first();

    if (!$existingOrder && in_array($action, ['remove', 'update_quantity', 'replace'], true)) {
        return [
            'success' => false,
            'message' => 'There is no existing pre-order to modify.',
        ];
    }

    $quantities = [];

    if ($existingOrder) {
        $rows = DB::table('pre_order_items')
            ->where('pre_order_id', $existingOrder->id)
            ->get();

        foreach ($rows as $row) {
            $quantities[(int) $row->product_id] = (int) $row->quantity;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Find existing product
    |--------------------------------------------------------------------------
    */

    $findExistingProduct = function (string $name) use ($reservation, $quantities) {
        if ($name === '') {
            return null;
        }

        $product = $this->findProduct(
            $name,
            (int) $reservation->hotel_id
        );

        if (!$product) {
            return null;
        }

        return isset($quantities[(int) $product->id])
            ? $product
            : null;
    };

    /*
    |--------------------------------------------------------------------------
    | Add item
    |--------------------------------------------------------------------------
    */

    if ($action === 'add') {
        if ($productName === '') {
            return ['success' => false, 'message' => 'Please specify the food item.'];
        }

        $product = $this->findProduct(
            $productName,
            (int) $reservation->hotel_id
        );

        if (!$product) {
            return [
                'success' => false,
                'message' => "I couldn't find {$productName} on the menu.",
            ];
        }

        $id = (int) $product->id;

        $quantities[$id] = ($quantities[$id] ?? 0) + $quantity;
    }

    /*
    |--------------------------------------------------------------------------
    | Remove item
    |--------------------------------------------------------------------------
    */

    elseif ($action === 'remove') {
        $product = $findExistingProduct($productName);

        if (!$product) {
            return [
                'success' => false,
                'message' => "{$productName} was not found in your existing pre-order.",
            ];
        }

        unset($quantities[(int) $product->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update quantity
    |--------------------------------------------------------------------------
    */

    elseif ($action === 'update_quantity') {
        $product = $findExistingProduct($productName);

        if (!$product) {
            return [
                'success' => false,
                'message' => "{$productName} was not found in your existing pre-order.",
            ];
        }

        $quantities[(int) $product->id] = $quantity;
    }

    /*
    |--------------------------------------------------------------------------
    | Replace old item with new item
    |--------------------------------------------------------------------------
    */

    elseif ($action === 'replace') {
        $replacementName = trim(
            (string) ($args['replacement_product_name'] ?? '')
        );

        $replacementQuantity = max(
            1,
            (int) ($args['replacement_quantity'] ?? 1)
        );

        if ($productName === '' || $replacementName === '') {
            return [
                'success' => false,
                'message' => 'Please specify both the old and replacement food items.',
            ];
        }

        $oldProduct = $findExistingProduct($productName);

        if (!$oldProduct) {
            return [
                'success' => false,
                'message' => "{$productName} was not found in your existing pre-order.",
            ];
        }

        $newProduct = $this->findProduct(
            $replacementName,
            (int) $reservation->hotel_id
        );

        if (!$newProduct) {
            return [
                'success' => false,
                'message' => "I couldn't find {$replacementName} on the menu.",
            ];
        }

        unset($quantities[(int) $oldProduct->id]);

        $newId = (int) $newProduct->id;

        $quantities[$newId] = ($quantities[$newId] ?? 0)
            + $replacementQuantity;
    }

    /*
    |--------------------------------------------------------------------------
    | Rebuild the order with all remaining items
    |--------------------------------------------------------------------------
    */

    $resolvedItems = [];

    if (!empty($quantities)) {
        $products = DB::table('products')
            ->where('hotel_id', $reservation->hotel_id)
            ->whereIn('id', array_keys($quantities))
            ->get()
            ->keyBy('id');

        foreach ($quantities as $productId => $qty) {
            $product = $products->get($productId);

            if (!$product) {
                return [
                    'success' => false,
                    'message' => 'An existing food item could not be loaded. No changes were saved.',
                ];
            }

            $resolvedItems[] = [
                'product' => $product,
                'quantity' => $qty,
            ];
        }
    }

    $order = $this->createOrUpdatePreOrder(
        $userId,
        $reservation,
        $resolvedItems
    );

    return [
        'success' => true,
        'message' => 'Your pre-order has been updated successfully.',
        'action' => $action,
        'pre_order' => [
            'id' => $order->id,
            'reservation_id' => $reservationId,
            'subtotal' => $order->subtotal,
            'tax' => $order->tax,
            'total_amount' => $order->total_amount,
            'items' => $order->items,
        ],
    ];
}

    /* ═══════════════════ USER TOOLS (unchanged) ═══════════════════ */

    private function bookTable(array $args, int $userId): array
    {
        $hotelId = isset($args['hotel_id']) ? (int) $args['hotel_id'] : null;
        $date    = isset($args['date'])    ? trim((string) $args['date']) : null;
        $time    = isset($args['time'])    ? trim((string) $args['time']) : null;
        $guests  = isset($args['guests'])  ? (int) $args['guests'] : null;

        $missing = [];
        if (!$hotelId) $missing[] = 'hotel';
        if (!$date)    $missing[] = 'date';
        if (!$time)    $missing[] = 'time';
        if (!$guests)  $missing[] = 'guests';

        if (!empty($missing)) {
            return [
                'success' => false,
                'missing' => $missing,
                'message' => 'Please provide: ' . implode(', ', $missing) . '.',
            ];
        }

        if ($userId <= 0) {
            return ['success' => false, 'message' => 'Please log in before booking a table.'];
        }

        if ($guests < 1) {
            return ['success' => false, 'message' => 'Number of guests must be at least 1.'];
        }

        $hotel = Hotel::select(['id', 'name'])->find($hotelId);
        if (!$hotel) {
            return ['success' => false, 'message' => 'The selected hotel was not found.'];
        }

        try {
            $reservationDateTime = \Carbon\Carbon::parse("{$date} {$time}");
        } catch (\Throwable) {
            return ['success' => false, 'message' => 'Invalid date or time.'];
        }

        if ($reservationDateTime->lt(now())) {
            return ['success' => false, 'message' => 'That time has already passed.'];
        }

        $table = $this->findAvailableTable($hotelId, $date, $time, $guests);
        if (!$table) {
            $suggestion = $this->findSuggestedTime($hotelId, $date, $time, $guests);
            return [
                'success'   => false,
                'available' => false,
                'message'   => $suggestion
                    ? "No table at {$time}. {$suggestion} is available."
                    : 'No table is available for that date and time.',
                'suggested_time' => $suggestion,
            ];
        }

        $reservation = $this->createReservation($userId, $hotelId, $table->id, $date, $time, $guests);

        return [
            'success' => true,
            'message' => 'Table reserved successfully.',
            'reservation' => [
                'id' => $reservation->id,
                'hotel_id' => $hotelId,
                'hotel_name' => $hotel->name,
                'table_id' => $table->id,
                'table_number' => $table->table_number,
                'date' => $date,
                'time' => $time,
                'guests' => $guests,
                'status' => $reservation->status,
            ],
            'next_step' => 'Ask the user whether they want to pre-order food.',
        ];
    }

    private function findAvailableTable(int $hotelId, string $date, string $time, int $guests)
    {
        $tables = DB::table('hotel_tables')
            ->where('hotel_id', $hotelId)
            ->where('capacity', '>=', $guests)
            ->where('status', 'available')
            ->orderBy('capacity')
            ->get();

        foreach ($tables as $table) {
            if (Schema::hasColumn('reservations', 'table_id')) {
                $query = DB::table('reservations')
                    ->where('hotel_id', $hotelId)
                    ->whereDate('reservation_date', $date)
                    ->whereTime('reservation_time', $time)
                    ->where('table_id', $table->id);
                if (Schema::hasColumn('reservations', 'status')) {
                    $query->whereNotIn('status', ['cancelled']);
                }
                if (!$query->exists()) return $table;
            } else {
                return $this->findAvailableTableByGuestCapacity($hotelId, $date, $time, $guests, $table);
            }
        }
        return null;
    }

    private function findAvailableTableByGuestCapacity(
        int $hotelId, string $date, string $time, int $guests, $candidateTable
    ) {
        $reservedGuests = DB::table('reservations')
            ->where('hotel_id', $hotelId)
            ->whereDate('reservation_date', $date)
            ->whereTime('reservation_time', $time)
            ->whereNotIn('status', ['cancelled'])
            ->sum('number_of_guests');

        $totalCapacity = DB::table('hotel_tables')
            ->where('hotel_id', $hotelId)
            ->where('status', 'available')
            ->sum('capacity');

        return (($totalCapacity - $reservedGuests) >= $guests) ? $candidateTable : null;
    }

    private function createReservation(
        int $userId, int $hotelId, int $tableId, string $date, string $time, int $guests
    ) {
        return DB::transaction(function () use ($userId, $hotelId, $tableId, $date, $time, $guests) {
            $data = [
                'user_id' => $userId,
                'hotel_id' => $hotelId,
                'reservation_date' => $date,
                'reservation_time' => $time,
                'number_of_guests' => $guests,
                'status' => 'confirmed',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (Schema::hasColumn('reservations', 'table_id')) {
                $data['table_id'] = $tableId;
            }
            if (Schema::hasColumn('reservations', 'reservation_end_time')) {
                $data['reservation_end_time'] = \Carbon\Carbon::parse("{$date} {$time}")
                    ->addHours(2)->format('H:i:s');
            }
            return Reservation::create($data);
        });
    }

    private function findSuggestedTime(
        int $hotelId, string $date, string $requestedTime, int $guests
    ): ?string {
        try {
            $requested = \Carbon\Carbon::parse("{$date} {$requestedTime}");
        } catch (\Throwable) {
            return null;
        }
        for ($i = 1; $i <= 8; $i++) {
            foreach ([-1, 1] as $direction) {
                $candidate = $requested->copy()->addMinutes($direction * $i * 30);
                if ($candidate->lt(now())) continue;
                $time = $candidate->format('H:i');
                if ($this->findAvailableTable($hotelId, $date, $time, $guests)) {
                    return $time;
                }
            }
        }
        return null;
    }

    private function placePreOrder(array $args, int $userId): array
    {
        $reservationId = isset($args['reservation_id']) ? (int) $args['reservation_id'] : null;
        $items = $args['items'] ?? [];

        if (!$reservationId) {
            return ['success' => false, 'message' => 'Please specify the reservation.'];
        }
        if (!is_array($items) || count($items) === 0) {
            return ['success' => false, 'message' => 'Please specify food items.'];
        }

        $reservation = Reservation::where('id', $reservationId)->where('user_id', $userId)->first();
        if (!$reservation) {
            return ['success' => false, 'message' => 'Reservation not found.'];
        }

        $status = strtolower((string) $reservation->status);
        if (in_array($status, ['cancelled', 'canceled', 'completed'], true)) {
            return ['success' => false, 'message' => "This reservation is {$status}."];
        }

        $resolvedItems = [];
        foreach ($items as $item) {
            $productName = $item['product_name'] ?? $item['name'] ?? null;
            $quantity = isset($item['quantity']) ? max(1, (int) $item['quantity']) : 1;
            if (!$productName) continue;

            $product = $this->findProduct($productName, (int) $reservation->hotel_id);
            if (!$product) {
                return ['success' => false, 'message' => "I couldn't find {$productName} on the menu."];
            }
            $resolvedItems[] = ['product' => $product, 'quantity' => $quantity];
        }

        if (!$resolvedItems) {
            return ['success' => false, 'message' => 'No valid food items were found.'];
        }

        $order = $this->createOrUpdatePreOrder($userId, $reservation, $resolvedItems);

        return [
            'success' => true,
            'message' => 'Pre-order placed successfully.',
            'pre_order' => [
                'id' => $order->id,
                'reservation_id' => $reservationId,
                'subtotal' => $order->subtotal,
                'tax' => $order->tax,
                'total_amount' => $order->total_amount,
                'items' => $order->items,
            ],
        ];
    }

    private function findProduct(string $name, int $hotelId)
    {
        if (!Schema::hasTable('products')) return null;

        $name = trim($name);
        if ($name === '') return null;

        $query = DB::table('products');
        if (Schema::hasColumn('products', 'hotel_id')) $query->where('hotel_id', $hotelId);
        if (Schema::hasColumn('products', 'is_available')) $query->where('is_available', 1);

        $exact = (clone $query)->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();
        if ($exact) return $exact;

        $like = (clone $query)->where(function ($q) use ($name) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%' . mb_strtolower($name) . '%'])
              ->orWhereRaw('? LIKE CONCAT("%", LOWER(name), "%")', [$name]);
        })->first();
        if ($like) return $like;

        $target = mb_strtolower($name);
        $target = preg_replace('/[^a-z0-9 ]/', '', $target);
        $targetWords = array_filter(explode(' ', $target), fn ($w) => strlen($w) >= 3);
        if (empty($targetWords)) return null;

        $best = null; $bestScore = 0;
        foreach ((clone $query)->get() as $product) {
            $candidate = mb_strtolower($product->name);
            $candidate = preg_replace('/[^a-z0-9 ]/', '', $candidate);
            similar_text($target, $candidate, $percent);

            $candidateWords = array_filter(explode(' ', $candidate), fn ($w) => strlen($w) >= 3);
            $wordScore = 0;
            foreach ($targetWords as $tw) {
                foreach ($candidateWords as $cw) {
                    similar_text($tw, $cw, $wp);
                    $wordScore = max($wordScore, $wp);
                }
            }
            $score = max($percent, $wordScore);
            if ($score > $bestScore) { $bestScore = $score; $best = $product; }
        }
        return ($best && $bestScore >= 60) ? $best : null;
    }

    private function createOrUpdatePreOrder(int $userId, $reservation, array $items)
    {
        return DB::transaction(function () use ($userId, $reservation, $items) {
            $subtotal = 0; $computedItems = [];
            foreach ($items as $item) {
                $product = $item['product'];
                $price = (float) ($product->price ?? 0);
                $quantity = (int) $item['quantity'];
                $lineTotal = $price * $quantity;
                $subtotal += $lineTotal;
                $computedItems[] = [
                    'product' => $product, 'quantity' => $quantity,
                    'unit_price' => $price, 'line_total' => $lineTotal,
                ];
            }
            $tax = round($subtotal * 0.05, 2);
            $total = round($subtotal + $tax, 2);

            $existing = DB::table('pre_orders')->where('reservation_id', $reservation->id)->first();
            if ($existing) {
                $orderId = $existing->id;
                $update = [
                    'user_id' => $userId ?: null,
                    'subtotal' => $subtotal, 'tax' => $tax, 'total' => $total,
                    'updated_at' => now(),
                ];
                if (Schema::hasColumn('pre_orders', 'status')) $update['status'] = 'pending';
                if (Schema::hasColumn('pre_orders', 'submitted_at')) $update['submitted_at'] = now();
                DB::table('pre_orders')->where('id', $orderId)->update($update);
                if (Schema::hasTable('pre_order_items')) {
                    DB::table('pre_order_items')->where('pre_order_id', $orderId)->delete();
                }
            } else {
                $insert = [
                    'user_id' => $userId ?: null,
                    'reservation_id' => $reservation->id,
                    'subtotal' => $subtotal, 'tax' => $tax, 'total' => $total,
                    'created_at' => now(), 'updated_at' => now(),
                ];
                if (Schema::hasColumn('pre_orders', 'status')) $insert['status'] = 'pending';
                if (Schema::hasColumn('pre_orders', 'submitted_at')) $insert['submitted_at'] = now();
                $orderId = DB::table('pre_orders')->insertGetId($insert);
            }

            if (Schema::hasTable('pre_order_items')) {
                foreach ($computedItems as $ci) {
                    $product = $ci['product'];
                    $row = [
                        'pre_order_id' => $orderId, 'product_id' => $product->id,
                        'quantity' => $ci['quantity'], 'created_at' => now(), 'updated_at' => now(),
                    ];
                    if (Schema::hasColumn('pre_order_items', 'product_name')) $row['product_name'] = $product->name;
                    if (Schema::hasColumn('pre_order_items', 'unit_price')) $row['unit_price'] = $ci['unit_price'];
                    if (Schema::hasColumn('pre_order_items', 'line_total')) $row['line_total'] = $ci['line_total'];
                    DB::table('pre_order_items')->insert($row);
                }
            }

            return (object) [
                'id' => $orderId,
                'subtotal' => $subtotal, 'tax' => $tax, 'total_amount' => $total,
                'items' => array_map(fn ($ci) => [
                    'name' => $ci['product']->name,
                    'quantity' => $ci['quantity'],
                    'line_total' => $ci['line_total'],
                ], $computedItems),
            ];
        });
    }

    private function listUserReservations(array $args, int $userId): array
    {
        $limit = isset($args['limit']) ? min(max((int) $args['limit'], 1), 10) : 10;
        $query = Reservation::where('user_id', $userId)->with(['hotel:id,name']);

        if (!empty($args['from'])) {
            try { $query->whereDate('reservation_date', '>=', \Carbon\Carbon::parse($args['from'])->toDateString()); } catch (\Throwable) {}
        }
        if (!empty($args['to'])) {
            try { $query->whereDate('reservation_date', '<=', \Carbon\Carbon::parse($args['to'])->toDateString()); } catch (\Throwable) {}
        }

        $reservations = $query->orderBy('reservation_date')->orderBy('reservation_time')->limit($limit)->get();

        return [
            'success' => true,
            'count' => $reservations->count(),
            'reservations' => $reservations->map(fn ($r) => [
                'id' => $r->id,
                'hotel_id' => $r->hotel_id,
                'hotel_name' => $r->hotel?->name,
                'date' => $r->reservation_date,
                'time' => $r->reservation_time,
                'guests' => $r->number_of_guests,
                'status' => $r->status,
            ])->values()->all(),
        ];
    }

    private function updateReservation(array $args, int $userId): array
    {
        $reservationId = isset($args['reservation_id']) ? (int) $args['reservation_id'] : null;
        if (!$reservationId) return ['success' => false, 'message' => 'Please provide the reservation ID.'];

        $reservation = Reservation::where('id', $reservationId)->where('user_id', $userId)->first();
        if (!$reservation) return ['success' => false, 'message' => 'Reservation not found.'];

        if (in_array(strtolower((string) $reservation->status), ['cancelled', 'canceled'], true)) {
            return ['success' => false, 'message' => 'This reservation is already cancelled.'];
        }

        $date = $args['date'] ?? null;
        $time = $args['time'] ?? null;
        $guests = isset($args['guests']) ? (int) $args['guests'] : null;

        if ($date || $time || $guests) {
            $newDate = $date ?? $reservation->reservation_date;
            $newTime = $time ?? $reservation->reservation_time;
            $newGuests = $guests ?? $reservation->number_of_guests;

            $table = $this->findAvailableTable((int) $reservation->hotel_id, $newDate, $newTime, $newGuests);
            if (!$table) return ['success' => false, 'message' => 'No table available for the new date/time.'];

            $reservation->reservation_date = $newDate;
            $reservation->reservation_time = $newTime;
            $reservation->number_of_guests = $newGuests;
            if (Schema::hasColumn('reservations', 'reservation_end_time')) {
                $reservation->reservation_end_time = \Carbon\Carbon::parse("{$newDate} {$newTime}")
                    ->addHours(2)->format('H:i:s');
            }
        }
        $reservation->save();

        return [
            'success' => true,
            'message' => 'Reservation updated.',
            'reservation' => [
                'id' => $reservation->id, 'hotel_id' => $reservation->hotel_id,
                'date' => $reservation->reservation_date, 'time' => $reservation->reservation_time,
                'guests' => $reservation->number_of_guests, 'status' => $reservation->status,
            ],
        ];
    }

    private function cancelReservation(array $args, int $userId): array
    {
        $reservationId = isset($args['reservation_id']) ? (int) $args['reservation_id'] : null;
        if (!$reservationId) return ['success' => false, 'message' => 'Please provide the reservation ID.'];

        $reservation = Reservation::where('id', $reservationId)->where('user_id', $userId)->first();
        if (!$reservation) return ['success' => false, 'message' => 'Reservation not found.'];

        $reservation->status = 'cancelled';
        if (Schema::hasColumn('reservations', 'cancellation_reason') && !empty($args['reason'])) {
            $reservation->cancellation_reason = $args['reason'];
        }
        $reservation->save();

        return ['success' => true, 'message' => 'Reservation cancelled.', 'reservation_id' => $reservation->id];
    }

    private function listHotels(): array
    {
        $hotels = Hotel::select(['id', 'name'])->where('status', 'active')->orderBy('name')->get();
        return [
            'success' => true,
            'hotels' => $hotels->map(fn ($h) => ['id' => $h->id, 'name' => $h->name])->values()->all(),
        ];
    }

    /* ═══════════════════ PUBLIC READ-ONLY ═══════════════════ */

    private function listMenu(array $args): array
    {
        if (!Schema::hasTable('products')) return ['success' => false, 'message' => 'Menu not available.'];

        $hotelId = !empty($args['hotel_id']) ? (int) $args['hotel_id'] : Hotel::where('status', 'active')->value('id');
        if (!$hotelId) return ['success' => false, 'message' => 'No active hotel.'];

        $q = DB::table('products')
            ->where('hotel_id', $hotelId)
            ->where('is_available', 1)
            ->orderBy('category')
            ->orderBy('sort_order');

        if (!empty($args['category'])) $q->where('category', $args['category']);
        if (!empty($args['vegetarian_only'])) $q->where('is_vegetarian', 1);

        $rows = $q->limit(60)->get(['id','name','price','category','is_vegetarian','description']);

        return [
            'success' => true,
            'hotel_id' => $hotelId,
            'count' => $rows->count(),
            'items' => $rows->map(fn ($r) => [
                'id' => $r->id, 'name' => $r->name, 'price' => (float) $r->price,
                'category' => $r->category, 'is_vegetarian' => (bool) $r->is_vegetarian,
                'description' => $r->description,
            ])->values()->all(),
        ];
    }

    private function listAvailableTables(array $args): array
    {
        $hotelId = !empty($args['hotel_id']) ? (int) $args['hotel_id'] : Hotel::where('status', 'active')->value('id');
        if (!$hotelId) return ['success' => false, 'message' => 'No active hotel.'];

        $min = !empty($args['min_capacity']) ? (int) $args['min_capacity'] : 1;

        $tables = HotelTable::where('hotel_id', $hotelId)
            ->where('status', 'available')
            ->where('capacity', '>=', $min)
            ->orderBy('capacity')
            ->get(['id','table_number','capacity']);

        return [
            'success' => true,
            'hotel_id' => $hotelId,
            'count' => $tables->count(),
            'tables' => $tables->map(fn ($t) => [
                'id' => $t->id, 'number' => $t->table_number, 'capacity' => (int) $t->capacity,
            ])->values()->all(),
        ];
    }

    private function hotelInfo(array $args): array
    {
        $hotelId = !empty($args['hotel_id']) ? (int) $args['hotel_id'] : null;
        $hotel = $hotelId
            ? Hotel::find($hotelId, ['id','name','address','phone','email','status'])
            : Hotel::where('status', 'active')->first(['id','name','address','phone','email','status']);

        if (!$hotel) return ['success' => false, 'message' => 'Hotel not found.'];
        return ['success' => true, 'hotel' => $hotel->toArray()];
    }

    /* ═══════════════════ GUEST TOOLS ═══════════════════ */


private function createGuestReservation(array $args, ?string $guestId): array
{
    $args['reservation_date'] = $args['reservation_date'] ?? $args['date'] ?? null;
    $args['reservation_time'] = $args['reservation_time'] ?? $args['time'] ?? null;
    $args['number_of_guests'] = $args['number_of_guests'] ?? $args['guests'] ?? null;

    $required = [
        'guest_name',
        'guest_phone',
        'reservation_date',
        'reservation_time',
        'number_of_guests',
    ];

    $missing = array_values(array_filter(
        $required,
        fn ($key) => empty($args[$key])
    ));

    if (!empty($missing)) {
        return [
            'success' => false,
            'missing' => $missing,
            'message' => 'Missing: ' . implode(', ', $missing) . '. Ask user for ONLY these.',
        ];
    }

    if (!$guestId) {
        return [
            'success' => false,
            'message' => 'Guest session expired. Please start a new conversation.',
        ];
    }

    $args['reservation_date'] = $this->normalizeDate(
        (string) $args['reservation_date']
    );

    $args['reservation_time'] = $this->normalizeTime(
        (string) $args['reservation_time']
    );

    $hotelId = !empty($args['hotel_id'])
        ? (int) $args['hotel_id']
        : Hotel::where('status', 'active')->value('id');

    $hotel = $hotelId
        ? Hotel::find($hotelId, ['id', 'name'])
        : null;

    if (!$hotel) {
        return ['success' => false, 'message' => 'No active hotel available.'];
    }

    $guests = (int) $args['number_of_guests'];

    if ($guests < 1) {
        return ['success' => false, 'message' => 'Guest count must be at least 1.'];
    }

    try {
        $dt = \Carbon\Carbon::parse(
            "{$args['reservation_date']} {$args['reservation_time']}"
        );
    } catch (\Throwable) {
        return ['success' => false, 'message' => 'Invalid date/time.'];
    }

    if ($dt->lt(now())) {
        return [
            'success' => false,
            'message' => 'That time has already passed. Pick a future time.',
        ];
    }

    $table = $this->findAvailableTable(
        (int) $hotel->id,
        $args['reservation_date'],
        $args['reservation_time'],
        $guests
    );

    if (!$table) {
        $suggestion = $this->findSuggestedTime(
            (int) $hotel->id,
            $args['reservation_date'],
            $args['reservation_time'],
            $guests
        );

        return [
            'success' => false,
            'available' => false,
            'message' => $suggestion
                ? "All tables are booked at {$args['reservation_time']}. Next available: {$suggestion}."
                : 'No tables available for that date.',
            'suggested_time' => $suggestion,
        ];
    }

    try {
        $reservation = DB::transaction(function () use (
            $args,
            $hotel,
            $table,
            $guests,
            $guestId
        ) {
            $data = [
                'user_id' => null,
                'guest_name' => trim((string) $args['guest_name']),
                'guest_phone' => (string) $args['guest_phone'],
                'guest_email' => $args['guest_email'] ?? null,
                'hotel_id' => $hotel->id,
                'reservation_date' => $args['reservation_date'],
                'reservation_time' => $args['reservation_time'],
                'number_of_guests' => $guests,
                'status' => 'pending',
                'notes' => $args['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('reservations', 'guest_token')) {
                $data['guest_token'] = $this->generateGuestToken(
                    $args['guest_name']
                );
            }

            if (Schema::hasColumn('reservations', 'guest_session_id')) {
                $data['guest_session_id'] = $guestId;
            }

            if (Schema::hasColumn('reservations', 'table_id')) {
                $data['table_id'] = $table->id;
            }

            if (Schema::hasColumn('reservations', 'reservation_end_time')) {
                $data['reservation_end_time'] = \Carbon\Carbon::parse(
                    "{$args['reservation_date']} {$args['reservation_time']}"
                )->addHours(2)->format('H:i:s');
            }

            $reservation = Reservation::create($data);

            if (method_exists($reservation, 'tables')) {
                $reservation->tables()->sync([$table->id]);
            }

            return $reservation;
        });
    } catch (\Throwable $e) {
        Log::error('[GuestReservation] DB error', [
            'args' => $args,
            'error' => $e->getMessage(),
        ]);

        return [
            'success' => false,
            'message' => 'Could not save reservation. Check DB columns.',
        ];
    }

    return [
        'success' => true,
        'message' => sprintf(
            'Reservation created for %s on %s at %s for %d guests.',
            $reservation->guest_name,
            $reservation->reservation_date,
            $reservation->reservation_time,
            $reservation->number_of_guests
        ),
        'reservation_id' => $reservation->id,
        'confirmation_url' => !empty($reservation->guest_token)
            ? url("/booking/{$reservation->guest_token}")
            : null,
        'reservation' => [
            'id' => $reservation->id,
            'guest_name' => $reservation->guest_name,
            'date' => $reservation->reservation_date,
            'time' => $reservation->reservation_time,
            'guests' => $reservation->number_of_guests,
            'table_number' => $table->table_number ?? null,
            'status' => $reservation->status,
        ],
        'next_step' => 'Confirm + show 3-4 popular menu items with prices + ask if they want to pre-order.',
    ];
}


    private function createGuestPreOrder(array $args, ?string $guestId): array
    {
        $reservationId = !empty($args['reservation_id']) ? (int) $args['reservation_id'] : null;
        $items = $args['items'] ?? [];

        if (!$reservationId) return ['success' => false, 'message' => 'Missing reservation_id.'];
        if (!is_array($items) || !count($items)) return ['success' => false, 'message' => 'Please list food items.'];

        $reservation = Reservation::where('id', $reservationId)->whereNull('user_id')->first();
        if (!$reservation) return ['success' => false, 'message' => 'Reservation not found or already linked.'];

        $status = strtolower((string) $reservation->status);
        if (in_array($status, ['cancelled','canceled','completed'], true)) {
            return ['success' => false, 'message' => "Reservation is {$status}."];
        }

        $resolved = [];
        $notFound = [];

        foreach ($items as $item) {
            $name = $item['product_name'] ?? $item['name'] ?? null;
            $qty  = max(1, (int) ($item['quantity'] ?? 1));

            $product = null;
            if (!empty($item['product_id'])) {
                $product = DB::table('products')->where('id', (int) $item['product_id'])->first();
            } elseif ($name) {
                $product = $this->findProduct($name, (int) $reservation->hotel_id);
            }

            if (!$product) { $notFound[] = $name ?? ('#' . ($item['product_id'] ?? '?')); continue; }

            $resolved[] = [
                'product' => $product,
                'quantity' => $qty,
                'matched_name' => $product->name,
                'exact_match' => strtolower(trim((string) $name)) === strtolower($product->name),
            ];
        }

        if (empty($resolved)) {
            $categories = DB::table('products')
                ->where('hotel_id', $reservation->hotel_id)
                ->where('is_available', 1)
                ->distinct()
                ->pluck('category')
                ->filter()->values()->all();

            return [
                'success' => false,
                'message' => 'None of those items are on the menu.',
                'not_found' => $notFound,
                'categories' => $categories,
                'hint' => 'Tell the user what categories we DO have.',
            ];
        }

        $order = $this->createOrUpdatePreOrder(0, $reservation, array_map(
            fn ($r) => ['product' => $r['product'], 'quantity' => $r['quantity']],
            $resolved
        ));

        return [
            'success' => true,
            'message' => sprintf('Pre-order placed. Total Rs. %s.', number_format($order->total_amount, 2)),
            'not_found' => $notFound,
            'pre_order' => [
                'id' => $order->id,
                'reservation_id' => $reservation->id,
                'subtotal' => $order->subtotal,
                'tax' => $order->tax,
                'total_amount' => $order->total_amount,
                'items' => $order->items,
            ],
            'next_step' => 'Show itemized summary with quantity × name — price. Then total.',
        ];
    }

    private function normalizeTime(string $t): string
    {
        $t = trim($t);
        if (preg_match('/^\d{1,2}:\d{2}$/', $t)) {
            [$h, $m] = explode(':', $t);
            return str_pad((int) $h, 2, '0', STR_PAD_LEFT) . ':' . $m;
        }
        if (preg_match('/^(\d{1,2})(?::(\d{2}))?\s*(am|pm)$/i', $t, $m)) {
            $h = (int) $m[1]; $min = $m[2] ?? '00'; $ampm = strtolower($m[3]);
            if ($ampm === 'pm' && $h < 12) $h += 12;
            if ($ampm === 'am' && $h === 12) $h = 0;
            return str_pad($h, 2, '0', STR_PAD_LEFT) . ':' . $min;
        }
        return $t;
    }

    private function normalizeDate(string $d): string
    {
        $d = strtolower(trim($d));
        if ($d === 'today')    return now()->toDateString();
        if ($d === 'tomorrow') return now()->addDay()->toDateString();
        try { return \Carbon\Carbon::parse($d)->toDateString(); } catch (\Throwable) { return $d; }
    }
}
