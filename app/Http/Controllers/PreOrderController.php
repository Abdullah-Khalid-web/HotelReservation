<?php

namespace App\Http\Controllers;

use App\Models\PreOrder;
use App\Models\Reservation;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PreOrderController extends Controller
{
    /**
     * List pre-orders (user sees own, admin/kitchen sees all).
     */
    public function index(Request $request)
    {
        $query = PreOrder::with(['reservation.hotel', 'user', 'items'])
            ->latest();

        // Non-admin/kitchen users only see their own
        if (! auth()->user()->hasAnyRole(['superadmin', 'admin', 'kitchen'])) {
            $query->where('user_id', auth()->id());
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $preOrders = $query->paginate(15)->withQueryString();

        $stats = [
            'pending'   => (clone $query)->where('status', 'pending')->count(),
            'confirmed' => (clone $query)->where('status', 'confirmed')->count(),
            'preparing' => (clone $query)->where('status', 'preparing')->count(),
        ];

        return view('pre-orders.index', compact('preOrders', 'stats'));
    }

    /**
     * Show the pre-order form for a reservation.
     */
/**
 * Show the pre-order form with a reservation dropdown.
 */
public function create(Request $request)
{
    $user    = auth()->user();
    $isAdmin = $user->hasAnyRole(['superadmin', 'admin']);

    // Confirmed, future reservations
    $query = Reservation::with(['hotel', 'preOrder'])
        ->where('status', 'confirmed')
        ->whereDate('reservation_date', '>=', today())
        ->orderBy('reservation_date')
        ->orderBy('reservation_time');

    if (! $isAdmin) {
        $query->where('user_id', $user->id);
    }

    $reservations = $query->get();

    // Reservations WITHOUT a pre-order (can be picked)
    $availableReservations = $reservations
        ->filter(fn ($r) => ! $r->preOrder)
        ->values();

    // Products — we'll show ALL products for the initially selected hotel;
    // Alpine handles the switch when the user changes reservation.
    // Build a hotel_id → products map.
    $hotelIds = $availableReservations->pluck('hotel_id')->unique();

    $productsByHotel = Product::query()
        ->whereIn('hotel_id', $hotelIds)
        ->where('is_available', true)
        ->orderBy('category')
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get()
        ->groupBy('hotel_id')
        ->map(function ($products) {
            return $products->groupBy('category')->map(function ($items) {
                return $items->map(fn ($p) => [
                    'id'                   => $p->id,
                    'name'                 => $p->name,
                    'price'                => (float) $p->price,
                    'description'          => $p->description,
                    'is_vegetarian'        => (bool) $p->is_vegetarian,
                    'stock_quantity'       => $p->stock_quantity,
                    'preparation_minutes'  => $p->preparation_minutes,
                    'category'             => $p->category,
                ])->values();
            });
        });

    // Also pass reservations summary for the dropdown
    $reservationOptions = $availableReservations->map(fn ($r) => [
        'id'               => $r->id,
        'hotel_id'         => $r->hotel_id,
        'hotel_name'       => $r->hotel?->name ?? 'Unknown Hotel',
        'date'             => $r->reservation_date->format('D, M d, Y'),
        'time'             => \Carbon\Carbon::parse($r->reservation_time)->format('h:i A'),
        'end_time'         => $r->reservation_end_time
                                ? \Carbon\Carbon::parse($r->reservation_end_time)->format('h:i A')
                                : null,
        'guests'           => $r->number_of_guests,
        'label'            => "#{$r->id} · {$r->hotel?->name} · "
                            . $r->reservation_date->format('M d')
                            . ' ' . \Carbon\Carbon::parse($r->reservation_time)->format('h:i A'),
    ])->values();

    return view('pre-orders.create', compact(
        'reservationOptions',
        'productsByHotel'
    ));
}


/**
 * Store a new pre-order.
 */
public function store(Request $request)
{
    $validated = $request->validate([
        'reservation_id'        => ['required', 'exists:reservations,id'],
        'items'                 => ['required', 'array', 'min:1'],
        'items.*.product_id'    => ['required', 'exists:products,id'],
        'items.*.quantity'      => ['required', 'integer', 'min:1', 'max:50'],
        'items.*.notes'         => ['nullable', 'string', 'max:255'],
        'special_instructions'  => ['nullable', 'string', 'max:1000'],
    ]);

    $reservation = Reservation::with('preOrder')->findOrFail($validated['reservation_id']);

    // ─── Authorization ───
    $user    = auth()->user();
    $isAdmin = $user->hasAnyRole(['superadmin', 'admin']);

    if (! $isAdmin && $reservation->user_id !== $user->id) {
        abort(403, 'You can only pre-order for your own reservations.');
    }

    // ─── Reservation state validation ───
    if ($reservation->status !== 'confirmed') {
        return back()->withInput()->withErrors([
            'reservation_id' => 'Pre-orders can only be added to confirmed reservations.',
        ]);
    }

    if ($reservation->reservation_date->lt(today())) {
        return back()->withInput()->withErrors([
            'reservation_id' => 'Cannot pre-order for past reservations.',
        ]);
    }

    if ($reservation->preOrder) {
        return redirect()
            ->route('pre-orders.edit', $reservation->preOrder)
            ->with('info', 'This reservation already has a pre-order. Edit it below.');
    }

    DB::beginTransaction();

    try {
        $preOrder = PreOrder::create([
            'reservation_id'       => $reservation->id,
            'user_id'              => auth()->id(),
            'status'               => PreOrder::STATUS_PENDING,
            'special_instructions' => $validated['special_instructions'] ?? null,
            'submitted_at'         => now(),
        ]);

        foreach ($validated['items'] as $item) {
            $product = Product::forHotel($reservation->hotel_id)
                ->findOrFail($item['product_id']);

            if (! $product->hasStock($item['quantity'])) {
                throw new \Exception("Insufficient stock for {$product->name}.");
            }

            $preOrder->items()->create([
                'product_id'   => $product->id,
                'quantity'     => $item['quantity'],
                'product_name' => $product->name,
                'unit_price'   => $product->price,
                'line_total'   => $product->price * $item['quantity'],
                'notes'        => $item['notes'] ?? null,
            ]);

            $product->decrementStock($item['quantity']);
        }

        $preOrder->recalculateTotals();

        DB::commit();

        return redirect()
            ->route('pre-orders.show', $preOrder)
            ->with('success', 'Pre-order placed successfully!');
    } catch (\Throwable $e) {
        DB::rollBack();

        return back()
            ->withInput()
            ->withErrors(['items' => $e->getMessage()]);
    }
}


    /**
     * Show a pre-order.
     */
    public function show(PreOrder $preOrder)
    {
        if ($preOrder->user_id !== auth()->id()
            && ! auth()->user()->hasAnyRole(['superadmin', 'admin', 'kitchen'])) {
            abort(403);
        }

        $preOrder->load(['reservation.hotel', 'user', 'items.product']);

        return view('pre-orders.show', compact('preOrder'));
    }

    /**
     * Edit a pre-order.
     */
/**
 * Edit a pre-order.
 */
public function edit(PreOrder $preOrder)
{
    if ($preOrder->user_id !== auth()->id()
        && ! auth()->user()->hasAnyRole(['superadmin', 'admin'])) {
        abort(403);
    }

    if (! $preOrder->isEditable()) {
        return redirect()
            ->route('pre-orders.show', $preOrder)
            ->with('error', 'This pre-order can no longer be edited.');
    }

    $reservation = $preOrder->reservation;
    $preOrder->load(['items.product', 'reservation.hotel']);

    // ─── Products for this hotel (grouped by category) ───
    $products = Product::forHotel($reservation->hotel_id)
        ->available()
        ->orderBy('category')
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get()
        ->groupBy('category')
        ->map(function ($items) {
            return $items->map(fn ($p) => [
                'id'                   => $p->id,
                'name'                 => $p->name,
                'price'                => (float) $p->price,
                'description'          => $p->description,
                'is_vegetarian'        => (bool) $p->is_vegetarian,
                'stock_quantity'       => $p->stock_quantity,
                'preparation_minutes'  => $p->preparation_minutes,
                'category'             => $p->category,
            ])->values();
        });

    // ─── Pre-fill cart with existing items ───
    $initialCart = $preOrder->items->mapWithKeys(fn ($item) => [
        $item->product_id => [
            'id'       => $item->product_id,
            'name'     => $item->product_name,
            'price'    => (float) $item->unit_price,
            'quantity' => (int) $item->quantity,
            'notes'    => $item->notes ?? '',
        ],
    ])->toArray();

    // ─── Reservation summary for display ───
    $reservationSummary = [
        'id'         => $reservation->id,
        'hotel_id'   => $reservation->hotel_id,
        'hotel_name' => $reservation->hotel?->name ?? 'Unknown Hotel',
        'date'       => $reservation->reservation_date->format('D, M d, Y'),
        'time'       => \Carbon\Carbon::parse($reservation->reservation_time)->format('h:i A'),
        'end_time'   => $reservation->reservation_end_time
                            ? \Carbon\Carbon::parse($reservation->reservation_end_time)->format('h:i A')
                            : null,
        'guests'     => $reservation->number_of_guests,
    ];

    return view('pre-orders.edit', compact(
        'preOrder',
        'reservation',
        'reservationSummary',
        'products',
        'initialCart'
    ));
}

    /**
     * Update a pre-order.
     */
public function update(Request $request, PreOrder $preOrder)
{
    if ($preOrder->user_id !== auth()->id()
        && ! auth()->user()->hasAnyRole(['superadmin', 'admin'])) {
        abort(403);
    }

    if (! $preOrder->isEditable()) {
        return back()->withErrors(['items' => 'This pre-order can no longer be edited.']);
    }

    $validated = $request->validate([
        'items'                 => ['required', 'array', 'min:1'],
        'items.*.product_id'    => ['required', 'exists:products,id'],
        'items.*.quantity'      => ['required', 'integer', 'min:1', 'max:50'],
        'items.*.notes'         => ['nullable', 'string', 'max:255'],
        'special_instructions'  => ['nullable', 'string', 'max:1000'],
    ]);

    DB::beginTransaction();

    try {
        $reservation = $preOrder->reservation;

        // ─── Build old quantities map (product_id => quantity) ───
        $oldQuantities = $preOrder->items
            ->pluck('quantity', 'product_id')
            ->toArray();

        // ─── Restore old stock ───
        foreach ($preOrder->items as $oldItem) {
            $oldItem->product?->incrementStock($oldItem->quantity);
        }

        // ─── Build new items + validate stock ───
        $newItemsData = [];
        foreach ($validated['items'] as $item) {
            $product = Product::forHotel($reservation->hotel_id)
                ->findOrFail($item['product_id']);

            if (! $product->hasStock($item['quantity'])) {
                throw new \Exception("Insufficient stock for {$product->name}.");
            }

            $newItemsData[] = [
                'product'  => $product,
                'quantity' => (int) $item['quantity'],
                'notes'    => $item['notes'] ?? null,
            ];
        }

        // ─── Delete old items ───
        $preOrder->items()->delete();

        // ─── Insert new items + decrement stock ───
        foreach ($newItemsData as $data) {
            $product = $data['product'];

            $preOrder->items()->create([
                'product_id'   => $product->id,
                'quantity'     => $data['quantity'],
                'product_name' => $product->name,
                'unit_price'   => $product->price,
                'line_total'   => $product->price * $data['quantity'],
                'notes'        => $data['notes'],
            ]);

            $product->decrementStock($data['quantity']);
        }

        $preOrder->update([
            'special_instructions' => $validated['special_instructions'] ?? null,
        ]);

        $preOrder->recalculateTotals();

        DB::commit();

        return redirect()
            ->route('pre-orders.show', $preOrder)
            ->with('success', 'Pre-order updated successfully.');
    } catch (\Throwable $e) {
        DB::rollBack();

        return back()
            ->withInput()
            ->withErrors(['items' => $e->getMessage()]);
    }
}

    /**
     * Cancel a pre-order.
     */
    public function destroy(PreOrder $preOrder)
    {
        if ($preOrder->user_id !== auth()->id()
            && ! auth()->user()->hasAnyRole(['superadmin', 'admin'])) {
            abort(403);
        }

        if (! $preOrder->isCancellable()) {
            return back()->withErrors(['items' => 'This pre-order can no longer be cancelled.']);
        }

        DB::beginTransaction();

        try {
            // Restore stock
            foreach ($preOrder->items as $item) {
                $item->product?->incrementStock($item->quantity);
            }

            $preOrder->markAsCancelled('Cancelled by user.');

            DB::commit();

            return redirect()
                ->route('pre-orders.index')
                ->with('success', 'Pre-order cancelled.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors(['items' => $e->getMessage()]);
        }
    }


}
