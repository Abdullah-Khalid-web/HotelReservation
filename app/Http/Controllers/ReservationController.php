<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Hotel;
use App\Models\HotelTable;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    private function canSeeAll(): bool
    {
        return auth()->user()->hasAnyRole(['superadmin', 'admin']);
    }

    private function authorizeReservation(Reservation $reservation): void
    {
        if ($this->canSeeAll()) return;

        if ($reservation->user_id !== auth()->id()) {
            abort(403, 'You can only access your own reservations.');
        }
    }

    /**
     * Compute the effective end time.
     * - If empty → start + 2 hours
     * - If earlier than start → treat as crossing midnight (kept as-is)
     */
    private function resolveEndTime(string $startTime, ?string $endTime): string
    {
        if (! $endTime) {
            return Carbon::parse($startTime)->addHours(2)->format('H:i');
        }

        return $endTime;
    }

    public function index()
    {
        $query = Reservation::with(['user', 'hotel', 'tables'])->latest();

        if (! $this->canSeeAll()) {
            $query->where('user_id', auth()->id());
        }

        $reservations = $query->paginate(10);

        $baseQuery = Reservation::query();
        if (! $this->canSeeAll()) {
            $baseQuery->where('user_id', auth()->id());
        }

        $stats = [
            'total'     => (clone $baseQuery)->count(),
            'pending'   => (clone $baseQuery)->where('status', 'pending')->count(),
            'confirmed' => (clone $baseQuery)->where('status', 'confirmed')->count(),
            'cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
        ];

        return view('reservations.index', compact('reservations', 'stats'));
    }

    public function create()
    {
        $hotels = Hotel::orderBy('name')->get();

        return view('reservations.create', compact('hotels'));
    }

    /**
     * AJAX availability search.
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'hotel_id'             => ['required', 'exists:hotels,id'],
            'reservation_date'     => ['required', 'date', 'after_or_equal:today'],
            'reservation_time'     => ['required', 'date_format:H:i'],
            'reservation_end_time' => ['nullable', 'date_format:H:i'],
            'number_of_guests'     => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $startTime = $validated['reservation_time'];
        $endTime   = $this->resolveEndTime($startTime, $validated['reservation_end_time'] ?? null);

        // Get unique tables — already deduped by DB via distinct()
        $tables = HotelTable::query()
            ->where('hotel_id', $validated['hotel_id'])
            ->where('status', 'available')
            ->where('capacity', '>=', $validated['number_of_guests'])
            ->orderBy('capacity')
            ->get();

        // Filter by availability + force unique by id
        $availableTables = $tables
            ->filter(fn ($table) => Reservation::isTableAvailable(
                $table->id,
                $validated['reservation_date'],
                $startTime,
                $endTime
            ))
            ->unique('id')
            ->values()
            ->map(fn ($table) => [
                'id'           => $table->id,
                'table_number' => $table->table_number,
                'capacity'     => $table->capacity,
                'hotel_id'     => $table->hotel_id,
            ])
            ->toArray();

        return response()->json([
            'start_time' => $startTime,
            'end_time'   => $endTime,
            'tables'     => $availableTables,
        ]);
    }

    /**
     * Store a new reservation (auto-confirmed if table still available).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hotel_id'             => ['required', 'exists:hotels,id'],
            'reservation_date'     => ['required', 'date', 'after_or_equal:today'],
            'reservation_time'     => ['required', 'date_format:H:i'],
            'reservation_end_time' => ['nullable', 'date_format:H:i'],
            'number_of_guests'     => ['required', 'integer', 'min:1', 'max:50'],
            'notes'                => ['nullable', 'string', 'max:1000'],
            'tables'               => ['required', 'array', 'min:1'],
            'tables.*'             => ['exists:hotel_tables,id'],
        ]);

        $startTime = $validated['reservation_time'];
        $endTime   = $this->resolveEndTime($startTime, $validated['reservation_end_time'] ?? null);

        // Re-verify availability
        $conflicts = [];
        foreach ($validated['tables'] as $tableId) {
            if (! Reservation::isTableAvailable(
                $tableId,
                $validated['reservation_date'],
                $startTime,
                $endTime
            )) {
                $conflicts[] = $tableId;
            }
        }

        if (! empty($conflicts)) {
            return back()
                ->withInput()
                ->withErrors([
                    'tables' => 'Some tables are no longer available. Please search again.',
                ]);
        }

        $reservation = Reservation::create([
            'user_id'              => auth()->id(),
            'hotel_id'             => $validated['hotel_id'],
            'reservation_date'     => $validated['reservation_date'],
            'reservation_time'     => $startTime,
            'reservation_end_time' => $endTime,
            'number_of_guests'     => $validated['number_of_guests'],
            'status'               => 'confirmed',
            'notes'                => $validated['notes'] ?? null,
        ]);

        foreach (array_unique($validated['tables']) as $tableId) {
            $reservation->items()->create(['table_id' => $tableId]);
        }

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', 'Reservation confirmed!');
    }

    public function show(string $id)
    {
        $reservation = Reservation::with(['user', 'hotel', 'tables'])->findOrFail($id);

        $this->authorizeReservation($reservation);

        return view('reservations.show', compact('reservation'));
    }

    public function edit(string $id)
    {
        $reservation = Reservation::with('tables')->findOrFail($id);

        $this->authorizeReservation($reservation);

        $hotels = Hotel::orderBy('name')->get();
        $tables = HotelTable::with('hotel')->orderBy('table_number')->get();

        return view('reservations.edit', compact('reservation', 'hotels', 'tables'));
    }

    public function update(Request $request, string $id)
    {
        $reservation = Reservation::findOrFail($id);

        $this->authorizeReservation($reservation);

        $validated = $request->validate([
            'hotel_id'             => ['required', 'exists:hotels,id'],
            'reservation_date'     => ['required', 'date'],
            'reservation_time'     => ['required', 'date_format:H:i'],
            'reservation_end_time' => ['nullable', 'date_format:H:i'],
            'number_of_guests'     => ['required', 'integer', 'min:1', 'max:50'],
            'status'               => ['required', 'in:pending,confirmed,cancelled,completed'],
            'notes'                => ['nullable', 'string', 'max:1000'],
            'tables'               => ['nullable', 'array'],
            'tables.*'             => ['exists:hotel_tables,id'],
        ]);

        $startTime = $validated['reservation_time'];
        $endTime   = $this->resolveEndTime($startTime, $validated['reservation_end_time'] ?? null);

        if (! empty($validated['tables'])) {
            foreach ($validated['tables'] as $tableId) {
                if (! Reservation::isTableAvailable(
                    $tableId,
                    $validated['reservation_date'],
                    $startTime,
                    $endTime,
                    $reservation->id
                )) {
                    return back()->withInput()->withErrors([
                        'tables' => 'One or more selected tables conflict with another reservation.',
                    ]);
                }
            }
        }

        $reservation->update([
            'hotel_id'             => $validated['hotel_id'],
            'reservation_date'     => $validated['reservation_date'],
            'reservation_time'     => $startTime,
            'reservation_end_time' => $endTime,
            'number_of_guests'     => $validated['number_of_guests'],
            'status'               => $validated['status'],
            'notes'                => $validated['notes'] ?? null,
        ]);

        $reservation->items()->delete();

        if (! empty($validated['tables'])) {
            foreach (array_unique($validated['tables']) as $tableId) {
                $reservation->items()->create(['table_id' => $tableId]);
            }
        }

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', 'Reservation updated successfully.');
    }

    public function cancel(Request $request, string $id)
    {
        $reservation = Reservation::findOrFail($id);

        $this->authorizeReservation($reservation);

        $validated = $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $suggested = null;
        $firstTable = $reservation->tables->first();

        if ($firstTable) {
            $suggested = Reservation::suggestNextSlot(
                $firstTable->id,
                $reservation->reservation_date->format('Y-m-d'),
                $reservation->reservation_time,
                2
            );
        }

        $reservation->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'] ?? 'Cancelled by user.',
            'suggested_time'      => $suggested,
        ]);

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', 'Reservation cancelled.');
    }

    public function destroy(string $id)
    {
        $reservation = Reservation::findOrFail($id);

        $this->authorizeReservation($reservation);

        $reservation->items()->delete();
        $reservation->delete();

        return redirect()
            ->route('reservations.index')
            ->with('success', 'Reservation deleted successfully.');
    }
}
