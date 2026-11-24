<?php

namespace App\Http\Controllers;

use App\Models\HotelTable;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelTableController extends Controller
{
    public function index()
    {
        $tables = HotelTable::with('hotel')->latest()->paginate(10);

        $stats = [
            'total'       => HotelTable::count(),
            'available'   => HotelTable::where('status', 'available')->count(),
            'reserved'    => HotelTable::where('status', 'reserved')->count(),
            'maintenance' => HotelTable::where('status', 'maintenance')->count(),
        ];

        return view('tables.index', compact('tables', 'stats'));
    }

    public function create()
    {
        $hotels = Hotel::orderBy('name')->get();

        return view('tables.create', compact('hotels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hotel_id'     => ['required', 'exists:hotels,id'],
            'table_number' => ['required', 'string', 'max:50'],
            'capacity'     => ['required', 'integer', 'min:1', 'max:50'],
            'status'       => ['required', 'in:available,reserved,maintenance'],
        ]);

        HotelTable::create($validated);

        return redirect()
            ->route('tables.index')
            ->with('success', 'Table created successfully.');
    }
}
