<?php

// use App\Mail\TestEmail;
// use Illuminate\Http\Request;

// use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
// use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\TagController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\JWTAuthController;
// use App\Http\Controllers\SocialAuthController;
use App\Http\Middleware\IsAuth;
use App\Models\User;
use App\Http\Controllers\WishlistController;




// ------------------------------------------------
// Auth 
Route::post('register', [JWTAuthController::class, 'register']);
Route::post('login', [JWTAuthController::class, 'login']);

// Auth
Route::middleware([IsAuth::class])->group(function () {
    Route::get('me', [JWTAuthController::class, 'getUser']);
    Route::post('logout', [JWTAuthController::class, 'logout']);
    Route::patch('me', [JWTAuthController::class, 'patchUser']); // Add patch route for partial updates

    // Wishlist routes
    Route::post('/hotels/{hotelId}/wishlist', [WishlistController::class, 'toggleWishlist']);
    Route::get('/wishlist', [WishlistController::class, 'getWishlist']);

    // Room image upload & owner-specific endpoints
    Route::post('/rooms/{roomId}/images', [RoomController::class, 'uploadImage']);
    Route::get('/owner/rooms', [RoomController::class, 'ownerRooms']);
    Route::get('/owner/statistics', [RoomController::class, 'ownerStatistics']);

    // Owner bookings
    Route::get('/owner/bookings', [RoomController::class, 'ownerBookings']);

    // Hotel bookings
    Route::get('/hotels/{hotelId}/bookings', [RoomController::class, 'hotelBookings']);
});

// testing
Route::get('users', function () {

    $users = User::all();
    return response()->json([
        'users' => $users
    ]);
});

// --------------------------------------------------------------------------
// ------  Tags

// public routes (guest)
Route::get("/tags", [TagController::class, "index"]);
Route::get("/tags/search/{name}", [TagController::class, "search"]);

// for admin
Route::get("/tags/{id}", [TagController::class, "show"]);
Route::post("/tags", [TagController::class, "store"]);
Route::put("/tags/{id}", [TagController::class, "update"]);
Route::delete("/tags/{id}", [TagController::class, "destroy"]);
// --------------------------------------------------------------------------













//------------ hotel stuuff


// Public routes

Route::get("/homePageHotels", [HotelController::class, "homePageHotels"]);

Route::get('/owner/hotels/{id?}', [HotelController::class, 'ownerHotels']);


Route::get('/hotels', [HotelController::class, 'index']);
Route::get('/hotels/{id}', [HotelController::class, 'show']);
Route::get('/hotels/search/{term}', [HotelController::class, 'search']);


// Authenticated routes
Route::middleware([IsAuth::class])->group(function () {
    Route::post('/hotels', [HotelController::class, 'store']);
    Route::put('/hotels/{hotel}', [HotelController::class, 'update']);
    Route::delete('/hotels/{hotel}', [HotelController::class, 'destroy']);

    // Hotels owned by the authenticated owner
    // Route::get('/owner/hotels', [HotelController::class, 'ownerHotels']);
});





// Room routes


Route::get('/hotels/{hotelId}/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{id}', [RoomController::class, 'show']);
Route::post('/hotels/{hotelId}/rooms', [RoomController::class, 'store']);
Route::put('/rooms/{id}', [RoomController::class, 'update']);
Route::delete('/rooms/{id}', [RoomController::class, 'destroy']);

// New room routes
Route::get('/rooms/{id}/availability', [RoomController::class, 'checkAvailability']);
Route::get('/rooms/search', [RoomController::class, 'search']);

// Route::apiResource('hotels', HotelController::class);



// ------------------------------------------------





























// test api
Route::get("/", function () {
    return response()->json([
        'message' => 'Air booking Api'
    ], 200);
});




// Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect']);
// Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback']);

// Route::post('login/google', [AuthController::class, 'loginWithGoogle']);
// Route::post('login/facebook', [AuthController::class, 'loginWithFacebook']);

// Room availability
// Route::get('/rooms/{id}/availability', function ($id) {
//     // Implementation of the endpoint
// });

// // Room search with filters
// Route::get('/rooms/search', function () {
//     // Implementation of the endpoint
// });

// // Room pricing
// Route::get('/rooms/{id}/pricing', function ($id) {
//     // Implementation of the endpoint
// });

// // Room occupancy status
// Route::get('/rooms/{id}/status', function ($id) {
//     // Implementation of the endpoint
// });
