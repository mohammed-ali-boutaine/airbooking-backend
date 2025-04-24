<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Owner;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Resources\HotelResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
// use App\Models\Hotel;
// use App\Http\Resources\HotelResource;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * @var \App\Models\User $user
 */
class HotelController extends Controller
{
    /**
     * Display hotels for the home page.
     */
    public function homePageHotels()
    {
        try {
            $hotels = Hotel::with([
                // 'owner:id,name',
                'rooms.images' => function ($query) {
                    $query->limit(3);
                },
                'rooms' => function ($query) {
                    $query->limit(2);
                }
            ])
                ->limit(10)
                ->get();

            $testHotel = Hotel::with(['rooms.primaryImage'])->get();


            return response()->json(['data' => $hotels, 'test' => $testHotel]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching hotels: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display hotels owned by the authenticated user or a specific owner
     */
    public function ownerHotels(Request $request, $id = null)
    {
        try {
            $perPage = $request->input('per_page', 10);

            if ($id) {
                $owner = Owner::findOrFail($id);
                $hotels = $owner->hotels()->with('rooms')->paginate($perPage);
            } else {
                $id = auth()->id();
                $hotels = Hotel::where('owner_id', $id)
                    ->with('rooms')
                    ->paginate($perPage);
            }

            return response()->json([
                'data' => $hotels->items(),
                'meta' => [
                    'total' => $hotels->total(),
                    'per_page' => $hotels->perPage(),
                    'current_page' => $hotels->currentPage(),
                    'last_page' => $hotels->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching hotels: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of all hotels.
     */
    public function index()
    {
        try {
            $hotels = Hotel::with('owner:id,name,email')
                ->select('id', 'name', 'owner_id', 'city', 'country', 'phone', 'email', 'website')
                ->paginate(12);

            return response()->json(['data' => $hotels]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching hotels: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created hotel.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'        => 'required|string|max:255',
                'address'     => 'required|string',
                'city'        => 'required|string|max:100',
                'country'     => 'required|string|max:100',
                'description' => 'nullable|string',
                'coordinate'  => 'required|array',
                'coordinate.lat' => 'required|numeric',
                'coordinate.lng' => 'required|numeric',
                'profile_path' => 'nullable|file|image|max:10240',
                'cover_path' => 'nullable|file|image|max:10240',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|url|max:255',
                'tags' => 'nullable|array',
                'tags.*' => 'integer|exists:tags,id',
                'owner_id' => 'sometimes|exists:users,id',
            ]);

            // Handle file uploads with unique names
            if ($request->hasFile('profile_path')) {
                $file = $request->file('profile_path');
                $extension = $file->getClientOriginalExtension();
                $filename = 'profile_' . Str::uuid() . '.' . $extension;
                $validated['profile_path'] = $file->storeAs('hotel-info', $filename, 'public');
            }

            if ($request->hasFile('cover_path')) {
                $file = $request->file('cover_path');
                $extension = $file->getClientOriginalExtension();
                $filename = 'cover_' . Str::uuid() . '.' . $extension;
                $validated['cover_path'] = $file->storeAs('hotel-info', $filename, 'public');
            }

            $validated['coordinate'] = json_encode($validated['coordinate']);

            // Use owner_id from request if provided (for admin) or from auth user
            $user = JWTAuth::parseToken()->authenticate();
            $validated['owner_id'] = $request->input('owner_id', $user->id);

            $hotel = Hotel::create($validated);

            // Attach tags if provided
            if (!empty($validated['tags'])) {
                $hotel->tags()->attach($validated['tags']);
            }

            return response()->json(['data' => new HotelResource($hotel->load('tags')), 'message' => 'Hotel created successfully'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            if (isset($validated['profile_path']) && Storage::disk('public')->exists($validated['profile_path'])) {
                Storage::disk('public')->delete($validated['profile_path']);
            }

            if (isset($validated['cover_path']) && Storage::disk('public')->exists($validated['cover_path'])) {
                Storage::disk('public')->delete($validated['cover_path']);
            }

            return response()->json(['message' => 'Error creating hotel: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified hotel.
     */
    public function show($id)
    {
        try {
            $hotel = Hotel::with([
                'rooms.images',
                'tags:id,name',
                'owner:id,name'
            ])->findOrFail($id);

            return new HotelResource($hotel);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Hotel not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching hotel: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified hotel.
     */
    public function update(Request $request, Hotel $hotel)
    {
        try {
            // Authorization check
            if ($request->user()->cannot('update', $hotel)) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            // Validation
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'address' => 'sometimes|string|max:255',
                'city' => 'sometimes|string|max:100',
                'country' => 'sometimes|string|max:100',
                'description' => 'nullable|string',
                'profile_path' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,ico|max:2048',
                'cover_path' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,ico|max:4096',
                'coordinate' => 'sometimes|array',
                'coordinate.lat' => 'required_with:coordinate|numeric|between:-90,90',
                'coordinate.lng' => 'required_with:coordinate|numeric|between:-180,180',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|url|max:255',
                'owner_id' => 'sometimes|exists:users,id',
                'tags' => 'nullable|array',
                'tags.*' => 'integer|exists:tags,id',
            ]);

            // Store old file paths
            $oldProfilePath = $hotel->profile_path;
            $oldCoverPath = $hotel->cover_path;

            // Handle file uploads
            try {
                if ($request->hasFile('profile_path')) {
                    $file = $request->file('profile_path');
                    $extension = $file->getClientOriginalExtension();
                    $filename = 'profile_' . Str::uuid() . '.' . $extension;
                    $validated['profile_path'] = $file->storeAs('hotel-info', $filename, 'public');
                }

                if ($request->hasFile('cover_path')) {
                    $file = $request->file('cover_path');
                    $extension = $file->getClientOriginalExtension();
                    $filename = 'cover_' . Str::uuid() . '.' . $extension;
                    $validated['cover_path'] = $file->storeAs('hotel-info', $filename, 'public');
                }
            } catch (\Exception $e) {
                return response()->json(['message' => 'File upload failed'], 500);
            }

            // Handle coordinate
            if (isset($validated['coordinate'])) {
                $validated['coordinate'] = json_encode([
                    'lat' => (float)$validated['coordinate']['lat'],
                    'lng' => (float)$validated['coordinate']['lng']
                ]);
            }

            // Start transaction
            DB::beginTransaction();

            try {
                // Update hotel
                $hotel->update($validated);

                // Sync tags if provided
                if (isset($validated['tags'])) {
                    $hotel->tags()->sync($validated['tags']);
                }

                // Commit transaction
                DB::commit();

                // Clean up old files if update was successful
                if (isset($validated['profile_path']) && $oldProfilePath && $oldProfilePath !== $validated['profile_path']) {
                    Storage::disk('public')->delete($oldProfilePath);
                }

                if (isset($validated['cover_path']) && $oldCoverPath && $oldCoverPath !== $validated['cover_path']) {
                    Storage::disk('public')->delete($oldCoverPath);
                }

                return response()->json([
                    'data' => new HotelResource($hotel->load('tags')),
                    'request' => $validated,
                    'message' => 'Hotel updated successfully'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();

                // Clean up any newly uploaded files if transaction failed
                if (isset($validated['profile_path'])) {
                    Storage::disk('public')->delete($validated['profile_path']);
                }
                if (isset($validated['cover_path'])) {
                    Storage::disk('public')->delete($validated['cover_path']);
                }

                return response()->json(['message' => 'Hotel update failed'], 500);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred'], 500);
        }
    }

    /**
     * Remove the specified hotel.
     */
    public function destroy(Request $request, Hotel $hotel)
    {
        try {
            if ($request->user()->cannot('update', $hotel)) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            // Store file paths for cleanup after successful deletion
            $profilePath = $hotel->profile_path;
            $coverPath = $hotel->cover_path;

            $hotel->delete();

            if ($profilePath) {
                Storage::disk('public')->delete($profilePath);
            }

            if ($coverPath) {
                Storage::disk('public')->delete($coverPath);
            }

            return response()->json(['message' => 'Hotel deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting hotel: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Search hotels by name, city, or country with pagination.
     */
    public function search(Request $request, string $term)
    {
        try {
            $perPage = $request->input('per_page', 10);

            $hotels = Hotel::where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('city', 'like', "%{$term}%")
                    ->orWhere('country', 'like', "%{$term}%");
            })
                ->with('owner:id,name')
                ->paginate($perPage);

            return response()->json(['data' => $hotels]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error searching hotels: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get hotels with pagination.
     */
    public function getHotelsWithPagination(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');

            $hotels = Hotel::with(['owner:id,name', 'rooms'])
                ->orderBy($sortBy, $sortDirection)
                ->paginate($perPage);

            return response()->json(['data' => $hotels]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching hotels: ' . $e->getMessage()], 500);
        }
    }
}
