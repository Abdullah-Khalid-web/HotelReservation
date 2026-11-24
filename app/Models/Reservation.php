<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory;

protected $fillable = [
    'user_id',
    'guest_name',
    'guest_phone',
    'guest_email',
    'guest_token',
    'hotel_id',
    'reservation_date',
    'reservation_time',
    'reservation_end_time',
    'number_of_guests',
    'status',
    'cancellation_reason',
    'suggested_time',
    'notes',
];

protected static function booted()
{
    static::creating(function ($reservation) {
        // Auto-generate a public token for guest reservations
        if (!$reservation->user_id && !$reservation->guest_token) {
            $reservation->guest_token = \Illuminate\Support\Str::random(48);
        }
    });
}

    protected $casts = [
        'reservation_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function items()
    {
        return $this->hasMany(ReservationItem::class);
    }

    public function tables()
    {
        return $this->belongsToMany(
            HotelTable::class,
            'reservation_items',
            'reservation_id',
            'table_id'
        )->withTimestamps();
    }

    /**
     * Convert "HH:MM" into minutes since midnight.
     */
    private static function toMinutes(?string $time): int
    {
        if (! $time) return 0;

        [$h, $m] = array_map('intval', explode(':', $time));

        return ($h * 60) + $m;
    }

    /**
     * Check whether a table is free for the given time range.
     * Handles same-day and crossing-midnight ranges correctly.
     */
    public static function isTableAvailable(
        int $tableId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $ignoreReservationId = null
    ): bool {
        $newStart = self::toMinutes($startTime);
        $newEnd   = self::toMinutes($endTime);

        // If the new reservation crosses midnight (end <= start),
        // extend end by 24h so the numeric range is contiguous.
        if ($newEnd <= $newStart) {
            $newEnd += 24 * 60;
        }

        $query = self::query()
            ->where('reservation_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereHas('items', function ($q) use ($tableId) {
                $q->where('table_id', $tableId);
            });

        if ($ignoreReservationId) {
            $query->where('id', '!=', $ignoreReservationId);
        }

        $existing = $query->get(['id', 'reservation_time', 'reservation_end_time']);

        foreach ($existing as $r) {
            $rStart = self::toMinutes($r->reservation_time);
            $rEnd   = self::toMinutes($r->reservation_end_time);

            // Fallback: missing end → assume 2 hours
            if ($rEnd <= $rStart) {
                $rEnd = $rStart + 120;
            }

            // Crossing midnight
            if ($rEnd <= $rStart) {
                $rEnd += 24 * 60;
            }

            // Overlap test
            if ($newStart < $rEnd && $newEnd > $rStart) {
                return false;
            }
        }

        return true;
    }

    /**
     * Suggest the next available time slot (same duration) for a table.
     */
    public static function suggestNextSlot(
        int $tableId,
        string $date,
        string $startTime,
        int $durationHours = 2
    ): ?string {
        $start = Carbon::parse($date . ' ' . $startTime);

        for ($i = 0; $i < 48; $i++) {
            $candidateStart = $start->copy()->addMinutes($i * 30);
            $candidateEnd   = $candidateStart->copy()->addHours($durationHours);

            $available = self::isTableAvailable(
                $tableId,
                $date,
                $candidateStart->format('H:i'),
                $candidateEnd->format('H:i')
            );

            if ($available) {
                return $candidateStart->format('H:i');
            }
        }

        return null;
    }

    /**
     * Duration in hours (human readable, handles midnight).
     */
    public function getDurationAttribute(): string
    {
        $start = self::toMinutes($this->reservation_time);
        $end   = self::toMinutes($this->reservation_end_time);

        if ($end <= $start) {
            $end += 24 * 60;
        }

        $diff = $end - $start;

        $hours   = intdiv($diff, 60);
        $minutes = $diff % 60;

        if ($minutes === 0) {
            return $hours . ' ' . ($hours === 1 ? 'hour' : 'hours');
        }

        return $hours . 'h ' . $minutes . 'm';
    }
        // ⭐ YE ADD KARO — PreOrder relation (hasOne)
    public function preOrder()
    {
        return $this->hasOne(PreOrder::class);
    }

}
