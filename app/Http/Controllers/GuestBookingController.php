<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\HotelTable;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GuestBookingController extends Controller
{
    /**
     * Default reservation duration: 2 hours.
     */
    private function resolveEndTime(string $startTime, ?string $endTime = null): string
    {
        if (! $endTime) {
            return Carbon::parse($startTime)
                ->addHours(2)
                ->format('H:i');
        }

        return $endTime;
    }

    /**
     * Display guest booking form.
     * Do not load all tables here.
     */
    public function create()
    {
        $hotel = Hotel::where('status', 'active')
            ->firstOrFail();

        return view('guest.book', compact('hotel'));
    }

    /**
     * Search tables available for selected date and time.
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'reservation_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'reservation_time' => [
                'required',
                'date_format:H:i',
            ],
            'number_of_guests' => [
                'required',
                'integer',
                'min:1',
                'max:50',
            ],
        ]);

        $hotel = Hotel::where('status', 'active')
            ->firstOrFail();

        $startTime = $validated['reservation_time'];
        $endTime = $this->resolveEndTime($startTime);

        /*
         * First filter by hotel, status and capacity.
         * Then check actual reservation availability.
         */
        $tables = HotelTable::query()
            ->where('hotel_id', $hotel->id)
            ->where('status', 'available')
            ->where('capacity', '>=', $validated['number_of_guests'])
            ->orderBy('capacity')
            ->orderBy('table_number')
            ->get();

        $availableTables = $tables
            ->filter(function ($table) use ($validated, $startTime, $endTime) {
                return Reservation::isTableAvailable(
                    $table->id,
                    $validated['reservation_date'],
                    $startTime,
                    $endTime
                );
            })
            ->values()
            ->map(function ($table) {
                return [
                    'id'           => $table->id,
                    'table_number' => $table->table_number,
                    'capacity'     => $table->capacity,
                ];
            });

        return response()->json([
            'success' => true,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'tables' => $availableTables,
            'message' => $availableTables->isEmpty()
                ? 'No tables are available for your selected date and time.'
                : $availableTables->count() . ' available table(s) found.',
        ]);
    }

    /**
     * Store guest reservation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'guest_name' => [
                'required',
                'string',
                'max:100',
            ],
            'guest_phone' => [
                'required',
                'string',
                'max:30',
            ],
            'guest_email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'reservation_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'reservation_time' => [
                'required',
                'date_format:H:i',
            ],
            'number_of_guests' => [
                'required',
                'integer',
                'min:1',
                'max:50',
            ],
            'table_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'table_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:hotel_tables,id',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $hotel = Hotel::where('status', 'active')
            ->firstOrFail();

        $startTime = $validated['reservation_time'];
        $endTime = $this->resolveEndTime($startTime);

        $tableIds = array_values(array_unique($validated['table_ids']));

        $reservation = DB::transaction(function () use (
            $validated,
            $hotel,
            $startTime,
            $endTime,
            $tableIds
        ) {
            /*
             * Lock selected table records before checking availability.
             */
            $tables = HotelTable::query()
                ->whereIn('id', $tableIds)
                ->where('hotel_id', $hotel->id)
                ->where('status', 'available')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            /*
             * Ensure all selected tables belong to this hotel.
             */
            if ($tables->count() !== count($tableIds)) {
                throw ValidationException::withMessages([
                    'table_ids' => 'Some selected tables are invalid or unavailable. Please search again.',
                ]);
            }

            /*
             * Check individual table capacity.
             */
            $invalidCapacity = $tables->contains(function ($table) use ($validated) {
                return $table->capacity < $validated['number_of_guests'];
            });

            if ($invalidCapacity) {
                throw ValidationException::withMessages([
                    'table_ids' => 'The selected table capacity is insufficient for your guests.',
                ]);
            }

            /*
             * Recheck actual availability for the selected time.
             */
            foreach ($tables as $table) {
                if (! Reservation::isTableAvailable(
                    $table->id,
                    $validated['reservation_date'],
                    $startTime,
                    $endTime
                )) {
                    throw ValidationException::withMessages([
                        'table_ids' => 'One or more selected tables have already been reserved for this time. Please check availability again.',
                    ]);
                }
            }

            /*
             * Create guest reservation.
             */
            $reservation = Reservation::create([
                'user_id'              => null,
                'guest_name'           => $validated['guest_name'],
                'guest_phone'          => $validated['guest_phone'],
                'guest_email'          => $validated['guest_email'] ?? null,
                'hotel_id'             => $hotel->id,
                'reservation_date'     => $validated['reservation_date'],
                'reservation_time'     => $startTime,
                'reservation_end_time' => $endTime,
                'number_of_guests'     => $validated['number_of_guests'],
                'status'               => 'pending',
                'notes'                => $validated['notes'] ?? null,
            ]);

            /*
             * Use the same relationship structure as ReservationController.
             */
            foreach ($tableIds as $tableId) {
                $reservation->items()->create([
                    'table_id' => $tableId,
                ]);
            }

            return $reservation;
        });

        return redirect()
            ->route('guest.book.show', $reservation->guest_token)
            ->with(
                'success',
                'Your reservation has been submitted successfully and is awaiting confirmation. Save this link to manage your booking.'
            );
    }

    /**
     * Show guest reservation status.
     */
    public function show(string $token)
    {
        $reservation = Reservation::query()
            ->where('guest_token', $token)
            ->with(['hotel', 'tables'])
            ->firstOrFail();

        return view('guest.booking-status', compact('reservation'));
    }
}
