<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\Payments\StripeProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Str;

class CheckoutService
{
    public static function processCheckout(array $data)
    {
        $room_requirements = [];
        foreach ($data['room_requirements'] as $req) {
            [$id, $quantity] = explode(':', $req);

            if (isset($room_requirements[$id])) {
                $room_requirements[$id]['quantity'] += $quantity;
            } else {
                $room_requirements[$id] = [
                    'id' => (int) $id,
                    'quantity' => (int) $quantity,
                ];
            }
        }

        $checkIn = Carbon::parse($data['check_in'])->startOfDay();
        $checkOut = Carbon::parse($data['check_out'])->startOfDay();

        $requiredRoomsData = RoomsFindService::loadRequiredRooms(
            $room_requirements,
            $checkIn,
            $checkOut
        );

        // return $requiredRoomsData;
        if ($requiredRoomsData->IsEmpty()) {
            return ['error' => 'Sorry, requested rooms are no longer available, check for other Date'];
        }

        // return $requiredRoomsData;

        $allSelectedRooms = $requiredRoomsData['rooms'];

        // return $allSelectedRooms;

        $hotel = $requiredRoomsData['hotel'];

        if ($allSelectedRooms && $allSelectedRooms->isEmpty()) {
            return ['error' => 'Sorry, requested rooms are no longer available.'];
        }

        if (! RoomsFindService::validateSingleHotel($allSelectedRooms)) {
            return ['error' => 'You can only book rooms from one hotel at a time.'];
        }

        $userCountry = LocationService::fetchLocation();
        $hotelCountry = $hotel->city->state->country;

        $exchangeRate = 1;

        if ($userCountry['currency_code'] != $hotelCountry['currency_code']) {
            $exchangeRate = currencyExchangeRate($userCountry['currency_code'], $hotelCountry['currency_code']);
        }

        $nights = (int) $checkIn->diffInDays($checkOut);
        $sub_amount = $allSelectedRooms->sum(fn ($room) => $room->details->price * $nights);

        $discountData = [];
        if (! empty($data['coupon_code'])) {
            $discountData = DiscountCoupenService::validateCoupen(
                $sub_amount,
                $data['coupon_code'],
                $nights,
                $hotel->id,
                $userCountry['country_id'],
                $exchangeRate
            );

            if (isset($discountData['error'])) {
                return ['error' => $discountData['error']];
            }
        }

        $finalAmount = $discountData['final_amount'] ?? $sub_amount;
        // return [
        //     ...$discountData,
        //     'exchangeRate' => $exchangeRate,
        //     'sub_amount' => $sub_amount,
        //     'sub_Total' => $finalAmount * $exchangeRate,
        //     'final_Total' => $finalAmount * $exchangeRate,
        // ];

        try {
            DB::beginTransaction();

            $booking = Booking::create([
                'user_id' => Auth::id(),
                'hotel_id' => $hotel->id,
                'status' => Booking::STATUS_PENDING,
                'reference_number' => time().Str::upper(Str::random(4)).str_pad(Auth::id(), 4, '0', STR_PAD_LEFT),
                'sub_amount' => $sub_amount,
                'total_amount' => $finalAmount,
                'discount_amount' => $discountData['discount_amount'] ?? 0,
                'discount_id' => $discountData['coupon_id'] ?? null,
                'instructions' => $data['instructions'] ?? null,
                'currency' => $hotelCountry->currency_code,
                'guest_name' => $data['name'],
                'guest_email' => $data['email'],
                'guest_phone' => $data['phone'],
            ]);

            foreach ($allSelectedRooms as $room) {
                $booking->items()->create([
                    'room_id' => $room->id,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'price_at_booking' => $room->details->price,
                ]);
            }

            $roomsPayload = $allSelectedRooms->map(function ($room) use ($exchangeRate, $nights) {
                return [
                    'room_number' => $room->room_number,
                    'room_title' => $room->details->title,
                    'price' => round($room->details->price * $exchangeRate, 2),
                    'nights' => $nights,
                    'image' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?q=80&w=1170&auto=format&fit=crop',
                ];
            });

            $payload = [
                'booking_id' => $booking->id,
                'currency' => strtolower($userCountry['currency_code']),
                'hotel_name' => $hotel->name,
                'items' => $roomsPayload->ToArray(),
                'customer_email' => Auth::user()->email,
                'client_reference_id' => Auth::id(),
                'metadata' => [
                    'full_name' => Auth::user()->name,
                    'phone' => Auth::user()->phone,
                ],
                'discount_amount' => ($discountData['discount_amount'] ?? 0) * $exchangeRate ?? 0,
                'success_url' => route('booking.success'),
                'cancel_url' => route('booking.cancelPayment'),

                // 'success_url' => request()->is('api/*') ? null : route('booking.success'),
                // 'cancel_url' => request()->is('api/*') ? null : route('booking.cancelPayment'),
            ];

            // Log::channel('debug')->info('Payload to Create Session : ', $payload);

            $service = new PaymentService(new StripeProvider);
            // $service = new PaymentService(new RazorPayProvider());
            $paymentData = $service->initializeBooking($payload);
            // return $paymentData;

            // return [
            //     'booking_id' => $booking->id,
            //     'session_id' => $paymentData['session_id'],
            //     'currency' => $hotelCountry['currency_code'],
            //     'gateway' => $paymentData['gateway'],
            //     'status' => Payment::STATUS_PENDING,
            //     'amount' => $booking->total_amount,
            //     'converted_amount' => $finalAmount * $exchangeRate,
            //     'paid_currency' => $paymentData['currency'],
            //     'exchange_rate' => $exchangeRate,
            // ];

            Payment::create([
                'booking_id' => $booking->id,
                'session_id' => $paymentData['session_id'],
                'currency' => $hotelCountry['currency_code'],
                'gateway' => $paymentData['gateway'],
                'status' => Payment::STATUS_PENDING,
                'amount' => $booking->total_amount,
                'converted_amount' => round($finalAmount * $exchangeRate, 4),
                'paid_currency' => $paymentData['currency'],
                'exchange_rate' => $exchangeRate,
            ]);

            $booking->update(['status' => Booking::STATUS_PROCESSING]);
            DB::commit();

            return $paymentData;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking Error: '.$e->getMessage());
            Log::channel('debug')->info('Booking Exception at Controller: '.$e->getMessage());
            Log::channel('failures')->critical('Booking Exception at Controller: '.$e->getMessage());

            return false;
        }
    }
}
