<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HotelTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'table_number',
        'capacity',
        'status',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function reservationItems()
    {
        return $this->hasMany(ReservationItem::class, 'table_id');
    }
}
