<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookingApiController;
use App\Http\Controllers\API\HomeApiController;
use App\Http\Controllers\API\RoomApiController;
use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/stripe/webhook', [PaymentWebhookController::class, 'stripe']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => 'Success',
            'data' => $request->user(),
        ]);
    });

    Route::get('/bookings', [BookingApiController::class, 'index']);
    Route::post('/book', [BookingApiController::class, 'store']);
    Route::get('/booking/{id}', [BookingApiController::class, 'show']);
    Route::post('/booking/cancel', [BookingApiController::class, 'cancelBooking']);

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('/navlinks', function () {
    $navlinks = collect(config('menu.client.navbar'))
        ->keys()
        ->map(function ($item) {
            if (auth('sanctum')->user() && $item == 'Login') {
                return [
                    'title' => 'Profile',
                    'link' => 'profile',
                ];
            } else {
                return [
                    'title' => $item,
                    'link' => str_replace(' ', '-', strtolower($item)),
                ];
            }
        });

    return response()->json($navlinks);
});

Route::get('/rooms', [RoomApiController::class, 'index']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/home/special-offers', [HomeApiController::class, 'getSpecialOffers']);
Route::get('/home/featured-rooms', [HomeApiController::class, 'getFeaturedRooms']);
Route::get('/home/search-cities', [HomeApiController::class, 'getSearchCities']);
Route::get('/home/hotel-cities', [HomeApiController::class, 'getHotelCities']);
