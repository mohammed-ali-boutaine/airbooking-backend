<?php

use App\Mail\TestEmail;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\TagController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\JWTAuthController;
use App\Http\Middleware\IsAuth;

// auth 

Route::post('register', [JWTAuthController::class, 'register']);
Route::post('login', [JWTAuthController::class, 'login']);

// Route::post('login/google', [AuthController::class, 'loginWithGoogle']);
// Route::post('login/facebook', [AuthController::class, 'loginWithFacebook']);



Route::post('/rooms', [RoomController::class, 'store'])->middleware('isAuth');
Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{id}', [RoomController::class, 'show']);
Route::apiResource('hotels', HotelController::class);

// hotel stuuff

Route::get("/hotels", [HotelController::class, "index"]);
Route::get("/hotels/{id}", [HotelController::class, "show"]);
Route::post("/hotels", [HotelController::class, "store"])
->middleware('isAuth','is_hotel_owner');
Route::put("/hotels/{id}", [HotelController::class, "update"])
->middleware([IsAuth::class]);
Route::delete("/hotels/{id}", [HotelController::class, "destroy"])
->middleware([IsAuth::class]);

// ------------------------------------------------
// public routes (guest)
Route::get("/tags", [TagController::class, "index"]);
Route::get("/tags/{name}", [TagController::class, "search"]);






// ------------------------------------------------
// for admin
Route::get("/tags/{id}", [TagController::class, "show"]);
Route::post("/tags", [TagController::class, "store"]);
Route::put("/tags/{id}", [TagController::class, "update"]);
Route::delete("/tags/{id}", [TagController::class, "destroy"]);

// ------------------------------------------------
// for auth user
Route::middleware([IsAuth::class])->group(function () {
    Route::get('user', [JWTAuthController::class, 'getUser']);
    Route::post('logout', [JWTAuthController::class, 'logout']);
    Route::put('user', [JWTAuthController::class, 'updateUser']);
});


// test api
// Route::get("/mail", function () {
//     $email = 'mohammedaliboutaine@gmail.com';
//     Mail::to($email)->send(new TestEmail());
//     return response()->json([
//         'message' => 'Email sent'
//     ], 200);
// });



// test api
Route::get("/", function () {
    return response()->json([
        'message' => 'Air booking Api'
    ], 200);
});
