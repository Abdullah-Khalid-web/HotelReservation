<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrder extends Model
{
    use HasFactory;

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_PENDING   = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_READY     = 'ready';
    public const STATUS_SERVED    = 'served';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'reservation_id',
        'user_id',
        'status',
        'subtotal',
        'tax',
        'total',
        'special_instructions',
        'cancellation_reason',
        'submitted_at',
        'confirmed_at',
        'ready_at',
        'served_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'tax'          => 'decimal:2',
        'total'        => 'decimal:2',
        'submitted_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'ready_at'     => 'datetime',
        'served_at'    => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // ──────────────── Relationships ────────────────

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PreOrderItem::class);
    }

    // ──────────────── Scopes ────────────────

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [
            self::STATUS_CANCELLED,
            self::STATUS_SERVED,
        ]);
    }

    public function scopeForHotel($query, int $hotelId)
    {
        return $query->whereHas('reservation', function ($q) use ($hotelId) {
            $q->where('hotel_id', $hotelId);
        });
    }

    // ──────────────── Helpers ────────────────

    public function isEditable(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_PENDING,
        ], true);
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
        ], true);
    }

    public function recalculateTotals(float $taxRate = 0.05): void
    {
        $subtotal = $this->items()->sum('line_total');
        $tax      = round($subtotal * $taxRate, 2);

        $this->update([
            'subtotal' => $subtotal,
            'tax'      => $tax,
            'total'    => $subtotal + $tax,
        ]);
    }

    public function markAsSubmitted(): void
    {
        $this->update([
            'status'       => self::STATUS_PENDING,
            'submitted_at' => now(),
        ]);
    }

    public function markAsConfirmed(): void
    {
        $this->update([
            'status'       => self::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);
    }

    public function markAsPreparing(): void
    {
        $this->update(['status' => self::STATUS_PREPARING]);
    }

    public function markAsReady(): void
    {
        $this->update([
            'status'   => self::STATUS_READY,
            'ready_at' => now(),
        ]);
    }

    public function markAsServed(): void
    {
        $this->update([
            'status'    => self::STATUS_SERVED,
            'served_at' => now(),
        ]);
    }

    public function markAsCancelled(?string $reason = null): void
    {
        $this->update([
            'status'              => self::STATUS_CANCELLED,
            'cancellation_reason' => $reason,
            'cancelled_at'        => now(),
        ]);
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT     => 'slate',
            self::STATUS_PENDING   => 'amber',
            self::STATUS_CONFIRMED => 'blue',
            self::STATUS_PREPARING => 'indigo',
            self::STATUS_READY     => 'emerald',
            self::STATUS_SERVED    => 'green',
            self::STATUS_CANCELLED => 'rose',
            default                => 'slate',
        };
    }
}
