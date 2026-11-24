<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    /**
     * Show hotel settings.
     */
    public function index()
    {
        $hotel = Hotel::first();

        return view('hotel.index', compact('hotel'));
    }

    /**
     * Store the hotel.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Hotel::create($validated);

        return redirect()
            ->route('hotel.index')
            ->with('success', 'Hotel created successfully.');
    }

    /**
     * Update the hotel.
     */
    public function update(Request $request)
    {
        $hotel = Hotel::firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $hotel->update($validated);

        return redirect()
            ->route('hotel.index')
            ->with('success', 'Hotel updated successfully.');
    }
}
