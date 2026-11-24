<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pre_order_id',
        'product_id',
        'quantity',
        'product_name',
        'unit_price',
        'line_total',
        'notes',
    ];

    protected $casts = [
        'quantity'   => 'integer',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (PreOrderItem $item) {
            $item->line_total = round($item->unit_price * $item->quantity, 2);
        });

        static::saved(function (PreOrderItem $item) {
            $item->preOrder?->recalculateTotals();
        });

        static::deleted(function (PreOrderItem $item) {
            $item->preOrder?->recalculateTotals();
        });
    }

    // ──────────────── Relationships ────────────────

    public function preOrder()
    {
        return $this->belongsTo(PreOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    // ──────────────── Helpers ────────────────

    public function getFormattedUnitPriceAttribute(): string
    {
        return number_format((float) $this->unit_price, 2);
    }

    public function getFormattedLineTotalAttribute(): string
    {
        return number_format((float) $this->line_total, 2);
    }
}
