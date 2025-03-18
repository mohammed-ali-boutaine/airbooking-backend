<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        // $hotels = Hotel::all();

        return response()->json(Hotel::all(), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'description' => 'nullable|string',
            'profile_path' => 'nullable|string',
            'cover_path' => 'nullable|string',
            'coordinate' => 'required|string',
            'owner_id' => 'required|exists:users,id',
        ]);

        $hotel = Hotel::create($validated);

        return response()->json($hotel, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Hotel $hotel)
    {
        return response()->json($hotel, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hotel $hotel)
    {
        if ($request->user()->cannot('update', $hotel)) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:100',
            'country' => 'sometimes|string|max:100',
            'description' => 'nullable|string',
            'profile_path' => 'nullable|string',
            'cover_path' => 'nullable|string',
            'coordinate' => 'sometimes|string',
            'owner_id' => 'sometimes|exists:users,id',
        ]);

        $hotel->update($validated);

        return response()->json($hotel, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hotel $hotel)
    {
        $hotel->delete();
        return response()->json(['message' => 'Hotel deleted successfully'], Response::HTTP_NO_CONTENT);
    }
}
