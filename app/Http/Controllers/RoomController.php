<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($hotelId)
    {
        //
        $rooms = Room::with(['images', 'hotel'])
            ->where('hotel_id', $hotelId)
            ->get();
        return response()->json(['data' => $rooms], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $hotelId)
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
        $validated['hotel_id'] = $hotelId;

        $room = Room::create($validated);

        return response()->json(['message' => 'Room created successfully!', 'room' => $room], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $room = Room::with(['images', 'hotel', 'bookings'])->findOrFail($id);
        return response()->json(['data' => $room], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $room = Room::findOrFail($id);
        // update this
        $validator = Validator::make($request->all(), [
            'room_number' => 'sometimes|required',
            'type' => 'sometimes|required',
            'description' => 'sometimes|required',
            'price_per_night' => 'sometimes|required|numeric',
            'capacity' => 'sometimes|required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $room->update($request->all());
        return response()->json(['data' => $room], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $room = Room::findOrFail($id);
        $room->delete();
        return response()->json(null, 204);
    }


    public function uploadImage(Request $request, $roomId)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_primary' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $room = Room::findOrFail($roomId);
        $imagePath = $request->file('image')->store('room_images', 'public');

        // If this is marked as primary, remove primary status from other images
        if ($request->is_primary) {
            RoomImage::where('room_id', $roomId)->update(['is_primary' => false]);
        }

        $image = RoomImage::create([
            'room_id' => $roomId,
            'image_path' => $imagePath,
            'is_primary' => $request->is_primary ?? false
        ]);

        return response()->json(['data' => $image], 201);
    }
}
