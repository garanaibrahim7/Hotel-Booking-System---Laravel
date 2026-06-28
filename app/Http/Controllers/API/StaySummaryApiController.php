<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Hotel;
use App\Models\RoomDetail;
use App\Services\DiscountCoupenService;
use App\Services\LocationService;
use App\Services\RoomsFindService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class StaySummaryApiController extends Controller
{
    public function reviewStay()
    {
        $stay = session('stay', []);

        if (empty($stay) || empty($stay['items'])) {
            return response()->json([
                'success' => true,
                'rooms_count' => 0,
                'stay' => null,
                'summary' => null,
            ], 200);
        }

        // Log::channel('debug')->info('Stay Details : ', ['stay' => $stay]);

        $checkIn = session('booking_check_in');
        $checkOut = session('booking_check_out');

        $nights = ($checkIn && $checkOut) ? Carbon::parse($checkIn)->diffInDays($checkOut) : 1;
        $stayNights = $nights > 0 ? $nights : 1;

        $totals = collect($stay['items'])->reduce(function ($carry, $item) {
            $carry['subtotal'] += ($item['converted_base_price'] * $item['quantity']);
            $carry['grandTotal'] += ($item['converted_price'] * $item['quantity']);

            return $carry;
        }, ['subtotal' => 0, 'grandTotal' => 0]);

        $summary = [
            'stayNights' => $stayNights,
            'subtotal' => round($totals['subtotal'] * $stayNights, 2),
            'grandTotal' => round($totals['grandTotal'] * $stayNights, 2),
            'totalSavings' => round(($totals['subtotal'] - $totals['grandTotal']) * $stayNights, 2),
            'currency' => $stay['currency_symbol'] ?? '$',
            'offer_message' => $stay['offer_message'] ?? null,
            'error_message' => $stay['last_discount_error'] ?? null,
            'hotel_id' => $stay['hotel_id'],
        ];

        // Log::channel('debug')->info('Review Stay Summary : ', [
        //     'rooms_count' => count($stay['items']),
        //     'stay' => array_values($stay['items']),
        //     'summary' => $summary,
        // ]);

        return response()->json([
            'success' => true,
            'rooms_count' => count($stay['items']),
            'hotel_id' => $stay['hotel_id'],
            'stay' => array_values($stay['items']),
            'summary' => $summary,
            'coupon_code' => $stay['coupon_code'] ?? null,
            'offer_message' => $stay['offer_message'] ?? null,
            'offer_type' => $stay['offer_type'] ?? null,
            'check_in' => Carbon::parse($checkIn)->format('Y-m-d'),
            'check_out' => Carbon::parse($checkOut)->format('Y-m-d'),
        ], 200);
    }

    public function roomsToStay()
    {
        $stay = session('stay', []);

        // Log::channel('debug')->info('Request : ', Cookie::get());
        // Log::channel('debug')->info('Rooms To Stay : ', $stay);
        // Log::channel('debug')->info('Sending rooms Count : '.(isset($stay['items']) ? count($stay['items']) : 0));

        return response()->json([
            'success' => true,
            'rooms_count' => (isset($stay['items'])) ? count($stay['items']) : 0,
        ], 200);
    }

    public function addToStayList(Request $request)
    {
        // Log::channel('debug')->info('Request : ', ['Request' => $request->only(['room_detail_id', 'hotel_id', 'check_in', 'check_out', 'coupon_code'])]);

        // return response()->json([
        //     'success' => true,
        //     'show_modal' => true,
        //     'room_id' => $request->room_detail_id,
        //     'hotel_id' => $request->hotel_id,
        //     'coupon_code' => $request->coupon_code ?? null,
        //     'offer_message' => $request->offer_message ?? null,
        //     'offer_type' => $request->offer_type ?? null,
        // ], 200);
        $userCountry = LocationService::fetchLocation();
        $checkIn = $request->check_in ?? session('booking_check_in');
        $checkOut = $request->check_out ?? session('booking_check_out');

        if (! $checkIn || ! $checkOut) {
            return response()->json([
                'success' => true,
                'show_modal' => true,
                'room_id' => $request->room_detail_id,
                'hotel_id' => $request->hotel_id,
                'coupon_code' => $request->coupon_code ?? null,
                'offer_message' => $request->offer_message ?? null,
                'offer_type' => $request->offer_type ?? null,
            ], 200);
        }

        $room = RoomDetail::with('hotel.city.state.country')->findOrFail($request->room_detail_id);
        $hotel = $room->hotel;
        $nights = Carbon::parse($checkIn)->diffInDays($checkOut) ?: 1;

        $hotelCurrency = $hotel->city->state->country->currency_code;
        $exchangeRate = 1;
        if ($userCountry['currency_code'] != $hotelCurrency) {
            $exchangeRate = convertCurrency(1, $userCountry['currency_code'], $hotelCurrency);
        }

        // session()->forget('stay');
        $stay = session()->get('stay', [
            'items' => [],
            'hotel_id' => $hotel->id,
            'hotel_name' => $hotel->name,
            'coupon_code' => $request->coupon_code ?? null,
            'offer_message' => $request->offer_message ?? null,
            'offer_type' => $request->offer_type ?? null,
            'currency_symbol' => $userCountry['currency_symbol'],
        ]);

        // Log::channel('debug')->info('Stay Details : ', ['stay' => $stay]);

        if ($stay['hotel_id'] != $hotel->id) {
            return response()->json([
                'success' => false,
                'error' => "You can't add rooms from multiple Hotels. Clear your current summary first.",
            ], 400);
        }

        if (session()->has('picked_discount') && empty($stay['discount_id'])) {
            $discount = Discount::where('coupen_code', session()->get('picked_discount'))->first();
            if ($discount) {
                $stay['discount_id'] = $discount->id;
            }
        }

        if (isset($stay['items'][$room->id])) {
            $stay['items'][$room->id]['quantity']++;
        } else {
            $stay['items'][$room->id] = [
                'id' => $room->id,
                'title' => $room->title,
                'base_price' => $room->price,
                'converted_base_price' => $room->price * $exchangeRate,
                'price' => $room->price,
                'converted_price' => $room->price * $exchangeRate,
                'quantity' => 1,
                'image' => $room->cover_image,
            ];
        }

        $this->syncStayCalculations($stay, $nights, $hotel->id, $userCountry, $exchangeRate);

        session()->put('stay', $stay);
        session()->put('booking_hotel_id', $hotel->id);
        session(['booking_check_in' => $checkIn, 'booking_check_out' => $checkOut]);

        return response()->json([
            'success' => true,
            'show_modal' => false,
            'rooms_count' => count($stay['items']),
            'message' => 'Room successfully added to your stay summary list!',
        ], 200);
    }

    public function removeFromStay(Request $request, $id)
    {
        // Log::channel('debug')->info('Deleting: ' . $id);
        $stay = session()->get('stay', []);

        if (isset($stay['items'][$id])) {
            unset($stay['items'][$id]);

            if (! empty($stay['items'])) {
                $userCountry = LocationService::fetchLocation();
                $checkIn = session('booking_check_in');
                $checkOut = session('booking_check_out');
                $nights = Carbon::parse($checkIn)->diffInDays($checkOut) ?: 1;

                $hotel = Hotel::with('city.state.country')->find($stay['hotel_id']);
                $exchangeRate = 1;
                if ($hotel && $userCountry['currency_code'] != $hotel->city->state->country->currency_code) {
                    $exchangeRate = convertCurrency(1, $userCountry['currency_code'], $hotel->city->state->country->currency_code);
                }

                $this->syncStayCalculations($stay, $nights, $stay['hotel_id'], $userCountry, $exchangeRate);
            }

            session()->put('stay', $stay);
        }

        $cartCount = empty($stay['items']) ? 0 : count($stay['items']);

        if ($cartCount === 0) {
            session()->forget(['checkoutPayload', 'changedStay', 'stay', 'booking_hotel_id']);
        }
        session()->put('changedStay', true);

        return response()->json([
            'success' => true,
            'rooms_count' => $cartCount,
            'message' => 'Room removed successfully.',
        ], 200);
    }

    public function updateStayDates(Request $request)
    {
        $stay = session()->get('stay');
        $checkIn = $request->check_in;
        $checkOut = $request->check_out;
        $userCountry = LocationService::fetchLocation();

        if (! $stay || ! $checkIn || ! $checkOut) {
            return response()->json(['success' => false, 'message' => 'Invalid request date criteria.'], 400);
        }

        $roomRequirements = array_map(fn ($item) => [
            'id' => $item['id'],
            'quantity' => $item['quantity'],
        ], $stay['items']);

        $availability = RoomsFindService::roomsAvailability($roomRequirements, $checkIn, $checkOut);

        if ($availability->contains('available', false)) {
            return response()->json([
                'success' => false,
                'message' => 'Required rooms are not available for these selected dates. Try another range.',
            ], 422);
        }

        session(['booking_check_in' => $checkIn, 'booking_check_out' => $checkOut]);
        $nights = Carbon::parse($checkIn)->diffInDays($checkOut) ?: 1;

        $hotel = Hotel::with('city.state.country')->findOrFail($stay['hotel_id']);
        $exchangeRate = 1;
        if ($userCountry['currency_code'] != $hotel->city->state->country->currency_code) {
            $exchangeRate = convertCurrency(1, $userCountry['currency_code'], $hotel->city->state->country->currency_code);
        }

        $this->syncStayCalculations($stay, $nights, $hotel->id, $userCountry, $exchangeRate);
        session()->put('stay', $stay);

        $totals = collect($stay['items'])->reduce(function ($carry, $item) {
            $carry['subtotal'] += ($item['converted_base_price'] * $item['quantity']);
            $carry['grandTotal'] += ($item['converted_price'] * $item['quantity']);

            return $carry;
        }, ['subtotal' => 0, 'grandTotal' => 0]);

        return response()->json([
            'success' => true,
            'nights' => $nights.' '.\Str::plural('Night', $nights),
            'subtotal' => $stay['currency_symbol'].' '.number_format($totals['subtotal'] * $nights, 2),
            'discount' => $stay['currency_symbol'].' '.number_format(($totals['subtotal'] - $totals['grandTotal']) * $nights, 2),
            'grandTotal' => $stay['currency_symbol'].' '.number_format($totals['grandTotal'] * $nights, 2),
            'date_display' => Carbon::parse($checkIn)->format('d M').' — '.Carbon::parse($checkOut)->format('d M, Y'),
        ], 200);
    }

    public function generateCheckoutPayload(Request $request)
    {
        $stay = session()->get('stay', []);

        if (empty($stay) || empty($stay['items'])) {
            return response()->json(['success' => false, 'message' => 'Your selection list is empty.'], 400);
        }

        $checkIn = $request->check_in ?? session('booking_check_in');
        $checkOut = $request->check_out ?? session('booking_check_out');

        if (! $checkIn || ! $checkOut) {
            return response()->json(['success' => false, 'message' => 'Stay parameters dates are missing.'], 400);
        }

        $roomRequirements = array_map(fn ($item) => [
            'id' => $item['id'],
            'quantity' => $item['quantity'],
        ], $stay['items']);

        $availabilityData = RoomsFindService::loadRequiredRooms($roomRequirements, $checkIn, $checkOut);

        if ($availabilityData->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Selected rooms are no longer available.'], 422);
        }

        $availableRooms = $availabilityData['rooms'];
        $hotel = $availabilityData['hotel'];
        $nights = Carbon::parse($checkIn)->diffInDays($checkOut) ?: 1;

        $userCountry = LocationService::fetchLocation();
        $hotelCountry = $hotel->city->state->country;

        $exchangeRate = 1;
        if ($userCountry['currency_code'] != $hotelCountry->currency_code) {
            $exchangeRate = convertCurrency(1, $userCountry['currency_code'], $hotelCountry->currency_code);
        }

        $rawTotal = $availableRooms->sum('details.price') * $nights;
        $coupon_code = null;

        if (isset($stay['discount_id'])) {
            $discount = Discount::find($stay['discount_id']);
            if ($discount && $discount->active_status) {
                $coupon_code = $discount->coupen_code;
            }
        }

        if ($coupon_code) {
            $validatedDiscount = DiscountCoupenService::validateCoupen($rawTotal, $coupon_code, $nights, $hotel->id, $userCountry['country_id'], $exchangeRate);
            if (isset($validatedDiscount['error'])) {
                return response()->json(['success' => false, 'message' => $validatedDiscount['error']], 422);
            }
        } else {
            $validatedDiscount = [
                'coupon_id' => null,
                'coupon_code' => null,
                'discount_amount' => 0,
                'final_amount' => round($rawTotal, 2),
                'final_converted_amount' => round($rawTotal * $exchangeRate, 2),
            ];
        }

        $checkoutPayload = [
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'nights' => $nights,
            'currency_symbol' => $userCountry['currency_symbol'],
            'hotelSubTotal' => round($rawTotal * $exchangeRate, 2),
            'finalTotal' => $validatedDiscount['final_converted_amount'],
            'discountAmount' => round($validatedDiscount['discount_amount'] * $exchangeRate, 2),
            'hotel' => ['id' => $hotel->id, 'name' => $hotel->name, 'address' => $hotel->address],
        ];

        session()->put('checkoutPayload', $checkoutPayload);

        return response()->json([
            'success' => true,
            'checkoutPayload' => $checkoutPayload,
            'coupon_code' => $coupon_code,
        ], 200);
    }

    private function syncStayCalculations(&$stay, $nights, $hotelId, $userCountry, $exchangeRate)
    {
        $rawSubtotal = collect($stay['items'])->sum(function ($item) {
            return $item['base_price'] * $item['quantity'];
        });

        $discountCode = null;
        if (! empty($stay['temp_discount_id'])) {
            $discount = Discount::find($stay['temp_discount_id']);
            $discountCode = $discount ? $discount->coupen_code : null;
        } elseif (! empty($stay['discount_id'])) {
            $discount = Discount::find($stay['discount_id']);
            $discountCode = $discount ? $discount->coupen_code : null;
        }

        $discountRatio = 0;

        if ($discountCode && $rawSubtotal > 0) {
            $validated = DiscountCoupenService::getDiscountPreview(
                $rawSubtotal, $discountCode, $nights, $hotelId, $userCountry['country_id'], $exchangeRate
            );

            if (isset($validated['status']) && $validated['status'] === true) {
                $discountRatio = $validated['discount_amount'] / $rawSubtotal;
                $stay['discount_id'] = $validated['coupon_id'];
                $stay['offer_message'] = Discount::find($validated['coupon_id'])->message ?? null;
                unset($stay['temp_discount_id'], $stay['last_discount_error']);
            } else {
                if (! empty($stay['discount_id'])) {
                    $stay['temp_discount_id'] = $stay['discount_id'];
                    $stay['discount_id'] = null;
                }
                $stay['offer_message'] = null;
                $stay['last_discount_error'] = $validated['error'] ?? 'Invalid Discount';
            }
        }

        foreach ($stay['items'] as $key => $item) {
            $final = $item['base_price'] - ($item['base_price'] * $discountRatio);
            $stay['items'][$key]['price'] = $final > 0 ? $final : 0;
            $stay['items'][$key]['converted_price'] = round($stay['items'][$key]['price'] * $exchangeRate, 2);
            $stay['items'][$key]['converted_base_price'] = round($item['base_price'] * $exchangeRate, 2);
        }
    }
}
