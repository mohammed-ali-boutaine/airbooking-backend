<?php

namespace App\Http\Controllers;

use App\Http\Resources\HotelResource;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function homePageHotels()
    {
        $hotels = Hotel::with([
            'owner:id,name', // Owner
            'rooms.images' => function ($query) {
                $query->limit(3); // Limit images per room
            },
            'rooms' => function ($query) {
                $query->limit(2); // Show only 2 rooms per hotel
            }
        ])->limit(10)->get();
        return response()->json(compact('hotels'));

        // return HotelResource::collection($hotels);
    }



    //

    // $hotels = Hotel::all();

    // return response()->json(Hotel::with("rooms")->get(), Response::HTTP_OK);

    public function index()
    {
        $hotels = Hotel::with('owner:id,name,email') // Owner info
            ->select('id', 'name', 'owner_id')
            ->paginate(10);

        // return HotelTableResource::collection($hotels);
        return response()->json(compact('hotels'));
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'address'     => 'required|string',
            'city'        => 'required|string|max:100',
            'country'     => 'required|string|max:100',
            'description' => 'nullable|string',
            'coordinate'  => 'required|array', 
            'coordinate.lat' => 'required|numeric',
            'coordinate.lng' => 'required|numeric',
    
            'hotel_profile' => 'nullable|image|max:2048',
            'cover_path'    => 'nullable|image|max:4096',
        ]);

        // Handle file uploads
        if ($request->hasFile('hotel_profile')) {
            $validated['profile_path'] = $request->file('hotel_profile')->store('hotels/profiles', 'public');
        }
    
        if ($request->hasFile('cover_path')) {
            $validated['cover_path'] = $request->file('cover_path')->store('hotels/covers', 'public');
        }

        $validated['coordinate'] = json_encode($validated['coordinate']);
        $validated['owner_id'] = auth()->id();
    

        $hotel = Hotel::create($validated);

        return response()->json($hotel, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $id)
    {
        $hotel = Hotel::with([
            'rooms.images',
            'reviews.user:id,name',
            'user:id,name'
        ])->findOrFail($id);
    
        // return new HotelDetailResource($hotel);
        return response()->json(compact('hotel'));

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
