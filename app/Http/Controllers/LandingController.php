<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\HotelTable;
use App\Models\Product;

class LandingController extends Controller
{
    public function index()
    {
        $hotel = Hotel::where('status', 'active')->first();

        if (!$hotel) {
            return view('landing', [
                'hotel'  => null,
                'tables' => collect(),
                'menu'   => collect(),
            ]);
        }

        $menu = Product::where('hotel_id', $hotel->id)
            ->where('is_available', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        $tables = HotelTable::where('hotel_id', $hotel->id)
            ->where('status', 'available')
            ->orderBy('table_number')
            ->get();

        return view('landing', compact('hotel', 'tables', 'menu'));
    }
}
