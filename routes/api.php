<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookingApiController;
use App\Http\Controllers\API\HomeApiController;
use App\Http\Controllers\API\ProfileApiController;
use App\Http\Controllers\API\RoomApiController;
use App\Http\Controllers\API\StaySummaryApiController;
use App\Http\Controllers\API\SubscriptionApiController;
use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::post('/stripe/webhook', [PaymentWebhookController::class, 'stripe']);
Route::get('/subscription/plans', [SubscriptionApiController::class, 'index']);

Route::middleware('auth:web')->group(function () {

    Route::get('/subscription/plans/{plan}/checkout', [SubscriptionApiController::class, 'planSummary']);
    Route::post('/subscription/plans/subscribe', [SubscriptionApiController::class, 'planSubscribe']);
    Route::get('/subscriptions/{historyId}/summary', [SubscriptionApiController::class, 'subscriptionDetails']);
    Route::get('/current/plan', [SubscriptionApiController::class, 'currentPlan']);
    Route::post('/subscription/cancel', [SubscriptionApiController::class, 'cancel']);
    Route::get('/subscription/change-payment-method', [SubscriptionApiController::class, 'updatePaymentMethod']);
    Route::get('/subscription/manage', [SubscriptionApiController::class, 'manageSubscription']);

    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => 'Success',
            'data' => $request->user(),
        ]);
    });

    Route::get('/user/profile', [ProfileApiController::class, 'index']);


    Route::get('/booking/checkout', [BookingApiController::class, 'checkout']);
    Route::post('/book', [BookingApiController::class, 'store']);

    Route::post('/booking/apply-coupon', [BookingApiController::class, 'applyCoupon']);
    Route::post('/booking/remove-coupon', [BookingApiController::class, 'removeCoupon']);

    Route::get('/bookings', [BookingApiController::class, 'index']);
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

Route::prefix('booking/stay')->group(function () {
    Route::get('summary', [StaySummaryApiController::class, 'reviewStay']);
    Route::get('rooms', [StaySummaryApiController::class, 'roomsToStay']);
    Route::post('add', [StaySummaryApiController::class, 'addToStayList']);
    Route::delete('remove/{id}', [StaySummaryApiController::class, 'removeFromStay']);
    Route::post('update-dates', [StaySummaryApiController::class, 'updateStayDates']);
    Route::post('checkout-payload', [StaySummaryApiController::class, 'generateCheckoutPayload']);
});

Route::get('/rooms', [RoomApiController::class, 'index']);
Route::get('/hotels', [\App\Http\Controllers\API\HotelApiController::class, 'index']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/home/hero-image', [HomeApiController::class, 'heroImage']);
Route::get('/home/special-offers', [HomeApiController::class, 'getSpecialOffers']);
Route::get('/home/featured-rooms', [HomeApiController::class, 'getFeaturedRooms']);
Route::get('/home/search-cities', [HomeApiController::class, 'getSearchCities']);
Route::get('/home/hotel-cities', [HomeApiController::class, 'getHotelCities']);

Route::get('/cities', [HomeApiController::class, 'getCities']);

Route::post('/session/save-text', function (Request $request) {
    session()->put('my_text', $request->input('message'));

    Log::channel('debug')->info('Session ID : '.session()->getId());

    return response()->json([
        'success' => true,
        'saved_as' => session()->get('my_text'),
    ]);
});

Route::get('/session/get-text', function () {
    Log::channel('debug')->info('Session ID : '.session()->getId());

    // Log::channel('debug')->info('Session : '.session()->get('my_text'));
    return response()->json([
        'success' => true,
        // 'text_in_session' => "custom text",
        'text_in_session' => session()->get('my_text', 'Session is empty!'),
    ]);
});
