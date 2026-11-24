<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'status',
    ];

    public function tables()
    {
        return $this->hasMany(HotelTable::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }

}
