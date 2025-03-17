<?php

use App\Mail\TestEmail;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\TagController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\JWTAuthController;

Route::apiResource('hotels', HotelController::class);



// ------------------------------------------------
// public routes (guest)
Route::get("/tags", [TagController::class, "index"]);
Route::get("/tags/{name}", [TagController::class, "search"]);
Route::post('register', [JWTAuthController::class, 'register']);
Route::post('login', [JWTAuthController::class, 'login']);


// ------------------------------------------------
// for admin
Route::get("/tags/{id}", [TagController::class, "show"]);
Route::post("/tags", [TagController::class, "store"]);
Route::put("/tags/{id}", [TagController::class, "update"]);
Route::delete("/tags/{id}", [TagController::class, "destroy"]);

// ------------------------------------------------
// for auth user
Route::middleware([JwtMiddleware::class])->group(function () {
    Route::get('user', [JWTAuthController::class, 'getUser']);
    Route::post('logout', [JWTAuthController::class, 'logout']);
    Route::put('user', [JWTAuthController::class, 'updateUser']);
});


// test api
Route::get("/mail", function () {
    $email = 'mohammedaliboutaine@gmail.com';
    Mail::to($email)->send(new TestEmail());
    return response()->json([
        'message' => 'Email sent'
    ], 200);
});



// test api
Route::get("/", function () {
    return response()->json([
        'message' => 'Air booking Api'
    ], 200);
});
