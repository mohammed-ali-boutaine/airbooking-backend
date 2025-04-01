<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'bed_numbers' => 'required|integer|min:1',
            'number_of_guests' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'is_available' => 'boolean',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string',
        ]);

        $validated['owner_id'] = auth()->id();

        $room = Room::create($validated);
    
        return response()->json(['message' => 'Room created successfully!', 'room' => $room], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
