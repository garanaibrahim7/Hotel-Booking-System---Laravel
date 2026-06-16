<?php

use App\Events\BroadcastBookingStatus;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoomBlockController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomDetailsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\User\PasswordResetController;
use App\Http\Controllers\User\UserBookingController;
use App\Http\Controllers\User\UserHotelController;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\User\UserRoomController;
use App\Http\Controllers\UserController;
use App\Models\Booking;
use App\Models\City;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Stripe\StripeClient;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::view('/login', 'client.auth.login')->middleware('guest')->name('login');
Route::post('/login', LoginController::class)->middleware('guest')->name('login');

Route::view('/register', 'client.auth.register')->middleware('guest')->name('register');
Route::post('/register', [UserProfileController::class, 'register'])->middleware('guest')->name('register');

Route::get('/logout', function () {
    Auth::logout();
    session_abort();

    return redirect('/');
})
    ->middleware('auth')
    ->name('logout');

Route::get('/trunc', function () {
    DB::table('transactions')->truncate();

    return 'Truncate Done...';
});

Route::get('/test-stripe-payload', function () {
    // 1. Initialize Stripe Client using your secret key configuration
    $stripe = new StripeClient(config('services.stripe.secret'));

    try {
        // 2. Prepare the combined nested payload structure matching your Stripe Dashboard form fields
        $payload = [
            'name' => 'Test Premium Gold Tier',
            'description' => 'A temporary test package to analyze the return object structure.',
            'default_price_data' => [
                'unit_amount' => 49900,      // ₹499.00 or $499.00 (in lowest denominator/cents)
                'currency' => 'usd',          // Must be lower-case three-letter ISO code
                'recurring' => [
                    'interval' => 'month',    // day, week, month, or year
                    'interval_count' => 3,    // Bills every 3 months
                ],
            ],
        ];

        // 3. Fire the request using Stripe's product endpoint
        $stripeProductResponse = $stripe->products->create($payload);

        // 4. Clean up and structure the vital tracking tokens returned by the server
        $debugOutput = [
            'instruction' => 'Analyze the fields below to see where IDs are stored.',
            'stripe_product_id' => $stripeProductResponse->id, // Looks like: prod_R1x8XYZ...

            // 🚀 THIS IS THE IMPORTANT TOKEN: This is what you save to your migration schema table!
            'stripe_price_id' => $stripeProductResponse->default_price, // Looks like: price_1XYZ...

            'raw_full_response_object' => $stripeProductResponse->toArray()
        ];

        // Return a clean JSON preview directly to your browser tab
        return [
            'success' => true,
            'message' => 'Stripe successfully parsed your nested single-request payload!',
            'summary_tokens' => $debugOutput,
            'stripeProductResponse' => $stripeProductResponse
        ];

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error_message' => $e->getMessage(),
            'hint' => 'Ensure your STRIPE_SECRET is configured properly inside your .env configuration file.'
        ], 500);
    }
});

Route::get('/test/{intent}', function ($intent) {

    $stripeObj = new StripeClient(config('services.stripe.secret'));
    return $stripeObj->checkout->sessions->retrieve($intent);
    return '🚀 Success! Broadcast fired into channel: booking-tracker.'.$intent;

})->name('test');

Route::prefix('manager')
    ->name('manager.')
    ->middleware('manager-auth')
    ->group(function () {

        Route::get('/', [ManagerController::class, 'index'])->name('dashboard');
        Route::get('/bookings', [ManagerController::class, 'bookings'])->name('bookings.index');
        Route::get('/booking/{booking}', [ManagerController::class, 'show'])->name('bookings.show');
        Route::get('/bookings/print', [ManagerController::class, 'printList'])->name('bookings.print');

        Route::get('/bookings/live', [BookingController::class, 'liveBookings'])->name('bookings.live');

        Route::get('/booking/{booking}/mark-arrival', [BookingController::class, 'markArrival'])->name('booking.mark-arrival');
        Route::get('/booking/{booking}/mark-leaved', [BookingController::class, 'markLeaved'])->name('booking.mark-leaved');
        Route::get('/booking/{booking}/cancel-booking', [BookingController::class, 'cancelBooking'])->name('booking.cancel-booking');

    });

Route::prefix('admin')
    ->name('admin.')
    ->middleware('admin-auth')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // booking chart
        Route::get('/booking-chart-data', [AdminDashboardController::class, 'bookingChartData'])->name('booking-chart');
        Route::get('/financial-chart-data', [AdminDashboardController::class, 'financialChartData'])->name('financial-chart');

        Route::get('/calendar-data', [RoomController::class, 'getCalendarData'])->name('calendar-data');

        Route::get('/bookings/report', [BookingController::class, 'report'])->name('bookings.report');
        Route::get('/bookings/print', [BookingController::class, 'printList'])->name('bookings.print');
        Route::get('/bookings/live', [BookingController::class, 'liveBookings'])->name('bookings.live');
        Route::patch('/user/{id}/update-role', [UserController::class, 'updateRole'])->name('users.update-role');
        Route::get('/changeRoomStatus/{room}', [RoomController::class, 'changeRoomStatus'])->name('room.change-status');

        Route::get('/refunds', [RefundController::class, 'index'])->name('refunds');
        Route::get('/refund/{refund}', [RefundController::class, 'show'])->name('refund.show');
        Route::get('/refund/{refund}/process', [RefundController::class, 'processRefund'])->name('refund.process');
        Route::get('/refund/{refund}/reject', [RefundController::class, 'rejectRefund'])->name('refund.reject');

        Route::get('/refund/{refund_id}/cancel', function ($refund_id) {
            $stripeObj = new StripeClient(config('services.stripe.secret'));
            $response = $stripeObj->refunds->retrieve($refund_id);

            return $response;
        });

        Route::get('/transactions/{currency?}', [TransactionController::class, 'index'])->name('transactions');

        Route::resource('hotels', HotelController::class);
        Route::resource('rooms', RoomController::class);
        Route::resource('categories', RoomDetailsController::class);
        Route::resource('bookings', BookingController::class);
        Route::resource('reviews', ReviewController::class);
        Route::resource('users', UserController::class);
        Route::resource('subscription', SubscriptionController::class);

        Route::get('room/{id}/add-block', [RoomBlockController::class, 'createBlock'])->name('rooms.add-block');
        Route::post('room/add-block', [RoomBlockController::class, 'storeBlock'])->name('rooms.store-block');
        Route::get('room/blocks', [RoomBlockController::class, 'index'])->name('rooms.blocks');
        Route::delete('room/{roomblock}/remove-block', [RoomBlockController::class, 'destroy'])->name('rooms.remove-block');

        Route::get('reviews/toggle/{review}', [ReviewController::class, 'toggle'])->name('reviews.toggle')->withTrashed();
        Route::get('/room/bookings/{id}', [RoomController::class, 'bookings'])->name('room.bookings');

        Route::get('/booking/{booking}/mark-success', [BookingController::class, 'markSuccess'])->name('booking.mark-success');
        Route::get('/booking/{booking}/mark-arrival', [BookingController::class, 'markArrival'])->name('booking.mark-arrival');
        Route::get('/booking/{booking}/mark-leaved', [BookingController::class, 'markLeaved'])->name('booking.mark-leaved');
        Route::get('/booking/{booking}/cancel-booking', [BookingController::class, 'cancelBooking'])->name('booking.cancel-booking');

        Route::post('/booking/book', [BookingController::class, 'directBook'])->name('bookings.book');

        Route::post('/discounts/edit', [DiscountController::class, 'editForm'])->name('discounts.edit');
        Route::patch('/discounts/{discount}/toggle', [DiscountController::class, 'toggleActive'])->name('discounts.toggle');
        Route::get('/discounts/seasonal-offer', [DiscountController::class, 'createSeasonalOffer'])->name('discounts.seasonal.offer');
        Route::resource('discounts', DiscountController::class);
    });

Route::name('client.')
    ->middleware(['throttle:strict_api'])
    ->group(function () {

        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/rooms', [UserRoomController::class, 'index'])->name('rooms');

        Route::get('/rooms/{id}', [UserHotelController::class, 'show'])->name('room.explore');
        Route::get('/hotels', [UserHotelController::class, 'index'])->name('hotels.explore');

        Route::get('/room/{id}', [UserRoomController::class, 'show'])->name('room.details');
        Route::get('/room/{id}/availability', [UserRoomController::class, 'getRoomAvailability'])->name('room.availability');

        // Route::get('/bookings', [UserBookingController::class, 'index'])->name('bookings');

        Route::view('/contact', 'client.contact')->name('contact');
        Route::view('/aboutus', 'client.aboutus')->name('aboutus');

        Route::middleware('auth')
            ->group(function () {

                Route::get('/profile', [UserProfileController::class, 'profile'])->name('profile');
                Route::post('/profile/update', [UserProfileController::class, 'updateProfile'])->name('profile.update');

                Route::view('/delete-account', 'client.delete-acount-confirm')->name('delete-account');
                Route::delete('/delete-account', [UserProfileController::class, 'destroyAccount'])->name('delete-account');

                Route::get('/make-review/{reference_number}/{rating}', [ReviewController::class, 'create'])->name('make-review');
                Route::post('/make-review', [ReviewController::class, 'store'])->name('store-review');
                Route::get('/remove-review/{review}', [ReviewController::class, 'destroy'])->name('remove-review');

            });
    });

Route::prefix('password')
    ->name('password.')
    ->middleware('guest')
    ->group(function () {
        Route::view('/request', 'client.auth.forget-password-form')->name('request');
        Route::post('/email', [PasswordResetController::class, 'sendResetLink'])->name('email');
        Route::view('/reset/{token}', 'client.auth.reset-password-form')->name('reset');
        Route::post('/update', [PasswordResetController::class, 'updatePassword'])->name('update');
    });

Route::get('/password/changePassword', [PasswordResetController::class, 'changePassword'])
    ->name('password.change')
    ->middleware('auth');

Route::prefix('booking')->name('booking.')->group(function () {
    Route::post('/add-to-stay', [CartController::class, 'addToStayList'])->name('stay.add');
    Route::get('/remove-from-stay/{id}', [CartController::class, 'removeFromStay'])->name('stay.remove');

    Route::post('/updateStayDates', [CartController::class, 'updateStayDates'])->name('stay.update_dates');

    Route::get('/review-stay-summary', [CartController::class, 'reviewStay'])->name('stay.summary');
});

Route::prefix('booking')
    ->name('booking.')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [UserBookingController::class, 'index'])->name('all');
        Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
        Route::post('/checkout/process', [UserBookingController::class, 'store'])->name('checkout.process');

        Route::post('/discount/apply', [DiscountController::class, 'validateCouponCode'])->name('discount.apply');
        Route::post('/discount/remove', [DiscountController::class, 'removeCode'])->name('discount.remove');

        Route::get('/view/{referenceNumber}', [UserBookingController::class, 'show'])->name('view');

        Route::get('/success', [UserBookingController::class, 'paymentSuccess'])->name('success');
        Route::get('/payment-cancel', [UserBookingController::class, 'paymentCancel'])->name('cancelPayment');

        Route::get('/cancel/{reference_number}', [UserBookingController::class, 'cancelBooking'])->name('cancel');
        Route::post('/cancel/{id}/confirm', [UserBookingController::class, 'finalCancelBooking'])->name('cancel.confirm');

        Route::get('/print-invoice/{reference_no}', [UserBookingController::class, 'printInvoice'])->name('print_invoice');
        Route::get('/download-invoice/{reference_no}', [UserBookingController::class, 'downloadInvoice'])->name('download_invoice');

        Route::post('/process-payment', [UserBookingController::class, 'processPayment'])->name('payment.process');
    });

Route::get('/cities', function () {
    $cities = City::with('state.country')->get()->map(function ($city) {
        return (object) [
            'id' => $city->id,
            'full_name' => $city->name.' - '.
                $city->state->name.' ('.
                $city->state->country->name.')',
        ];
    });

    return $cities->toJson();
})->name('cities');
