<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'category',
        'is_vegetarian',
        'is_available',
        'stock_quantity',
        'preparation_minutes',
        'sort_order',
    ];

    protected $casts = [
        'price'               => 'decimal:2',
        'is_vegetarian'       => 'boolean',
        'is_available'        => 'boolean',
        'stock_quantity'      => 'integer',
        'preparation_minutes' => 'integer',
        'sort_order'          => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name)
                    . '-' . Str::random(5);
            }
        });
    }

    // ──────────────── Relationships ────────────────

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function preOrderItems()
    {
        return $this->hasMany(PreOrderItem::class);
    }

    // ──────────────── Scopes ────────────────

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeForHotel($query, int $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }

    public function scopeInStock($query, int $quantity = 1)
    {
        return $query->where(function ($q) use ($quantity) {
            $q->whereNull('stock_quantity')
              ->orWhere('stock_quantity', '>=', $quantity);
        });
    }

    // ──────────────── Helpers ────────────────

    public function hasStock(int $quantity = 1): bool
    {
        if ($this->stock_quantity === null) {
            return true;
        }

        return $this->stock_quantity >= $quantity;
    }

    public function decrementStock(int $quantity): void
    {
        if ($this->stock_quantity === null) {
            return;
        }

        $this->decrement('stock_quantity', $quantity);
    }

    public function incrementStock(int $quantity): void
    {
        if ($this->stock_quantity === null) {
            return;
        }

        $this->increment('stock_quantity', $quantity);
    }
}
