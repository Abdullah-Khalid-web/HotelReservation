<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReservationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'table_id',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function table()
    {
        return $this->belongsTo(HotelTable::class, 'table_id');
    }
}
