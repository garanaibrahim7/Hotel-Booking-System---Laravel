<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Hotel;
use App\Models\RoomDetail;
use App\Services\DiscountCoupenService;
use App\Services\LocationService;
use App\Services\RoomsFindService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Str;

class CartController extends Controller
{
    public function reviewStay()
    {
        $stay = session('stay', []);

        if (empty($stay) || empty($stay['items'])) {
            return view('client.booking.stay-summary', ['stay' => [], 'summary' => null]);
        }

        $checkIn = session('booking_check_in');
        $checkOut = session('booking_check_out');

        $nights = ($checkIn && $checkOut) ? \Carbon\Carbon::parse($checkIn)->diffInDays($checkOut) : 1;
        $stayNights = $nights > 0 ? $nights : 1;

        $totals = collect($stay['items'])->reduce(function ($carry, $item) {
            $carry['subtotal'] += ($item['converted_base_price'] * $item['quantity']);
            $carry['grandTotal'] += ($item['converted_price'] * $item['quantity']);
            return $carry;
        }, ['subtotal' => 0, 'grandTotal' => 0]);

        $summary = (object)[
            'stayNights'    => $stayNights,
            'subtotal'      => $totals['subtotal'] * $stayNights,
            'grandTotal'    => $totals['grandTotal'] * $stayNights,
            'totalSavings'  => ($totals['subtotal'] - $totals['grandTotal']) * $stayNights,
            'currency'      => $stay['currency_symbol'] ?? '$',
            'offer_message' => $stay['offer_message'] ?? null,
            'error_message' => $stay['last_discount_error'] ?? null,
            'hotel_id'      => $stay['hotel_id']
        ];

        // return $stay;
        return view('client.booking.stay-summary', compact('stay', 'summary'));
    }

    public function addToStayList(Request $request)
    {
        $userCountry = LocationService::fetchLocation();
        $checkIn = $request->check_in ?? session('booking_check_in');
        $checkOut = $request->check_out ?? session('booking_check_out');

        Log::channel('debug')->info("Check In: {$checkIn}, and Check Out: {$checkOut}");

        if (!$checkIn || !$checkOut) {
            return response()->json([
                'status' => 'need_dates',
                'room_id' => $request->room_detail_id,
                'hotel_id' => $request->hotel_id,
                'show_modal' => true
            ]);
        }

        $room = RoomDetail::with('hotel.city.state.country')->findOrFail($request->room_detail_id);
        $hotel = $room->hotel;
        $nights = \Carbon\Carbon::parse($checkIn)->diffInDays($checkOut) ?: 1;

        $hotelCurrency = $hotel->city->state->country->currency_code;
        $exchangeRate = 1;
        if ($userCountry['currency_code'] != $hotelCurrency) {
            $exchangeRate = convertCurrency(1, $userCountry['currency_code'], $hotelCurrency);
        }

        $stay = session()->get('stay', [
            'items' => [],
            'hotel_id' => $hotel->id,
            'hotel_name' => $hotel->name,
            'discount_id' => null,
            'offer_message' => null,
            'currency_symbol' => $userCountry['currency_symbol']
        ]);

        if ($stay['hotel_id'] != $hotel->id) {
            return response()->json(['success' => false, 'error' => "You can't add rooms from multiple Hotels"]);
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
                "id" => $room->id,
                "title" => $room->title,
                "base_price" => $room->price,
                "converted_base_price" => $room->price * $exchangeRate,
                "price" => $room->price,
                "converted_price" => $room->price * $exchangeRate,
                "quantity" => 1,
                "image" => $room->images->first()->path ?? 'default.jpg',
            ];
        }

        // $discount = \App\Models\Discount::where('active_status', true)
        //     ->where('required_code', false)
        //     ->where('starts_from', '<=', $today)
        //     ->where(function ($q) use ($today) {
        //         $q->where('ends_at', '>=', $today)->orWhereNull('ends_at');
        //     })
        //     ->where(function ($q) use ($hotel) {
        //         $q->where('hotel_id', $hotel->id)->orWhereNull('hotel_id');
        //     })
        //     ->where('min_nights', '<=', $nights)
        //     ->orderBy('hotel_id', 'desc')
        //     ->first();

        $this->syncStayCalculations($stay, $nights, $hotel->id, $userCountry, $exchangeRate);

        session()->put('stay', $stay);
        session()->put('booking_hotel_id', $hotel->id);
        session(['booking_check_in' => $checkIn, 'booking_check_out' => $checkOut]);

        return response()->json(['success' => true, 'message' => 'Room added and stay updated!']);
    }

    public function removeFromStay(Request $request, $id)
    {
        $stay = session()->get('stay', []);

        if (isset($stay['items'][$id])) {
            unset($stay['items'][$id]);

            if (!empty($stay['items'])) {
                $userCountry = LocationService::fetchLocation();
                $checkIn = session('booking_check_in');
                $checkOut = session('booking_check_out');
                $nights = \Carbon\Carbon::parse($checkIn)->diffInDays($checkOut) ?: 1;

                $hotel = Hotel::with('city.state.country')->find($stay['hotel_id']);
                $exchangeRate = 1;
                if ($hotel && $userCountry['currency_code'] != $hotel->city->state->country->currency_code) {
                    $exchangeRate = convertCurrency(1, $userCountry['currency_code'], $hotel->city->state->country->currency_code);
                }

                $this->syncStayCalculations($stay, $nights, $stay['hotel_id'], $userCountry, $exchangeRate);
            }

            session()->put('stay', $stay);
        }

        if (empty($stay['items'])) {
            session()->forget('checkoutPayload');
            session()->forget('changedStay');
            session()->forget('stay');
            session()->forget('booking_hotel_id');
        }
        session()->put('changedStay', true);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'cart_count' => empty($stay['items']) ? 0 : count($stay['items']),
            ]);
        }
        return back()->with('success', 'Room removed!');
    }

    public function checkout(Request $request)
    {
        $stay = session()->get('stay', []);

        if (empty($stay) || empty($stay['items'])) {
            return redirect()->route('client.hotels.explore')->with('error', 'Your selection is empty!');
        }

        $checkIn = $request->check_in ?? session('booking_check_in');
        $checkOut = $request->check_out ?? session('booking_check_out');

        if (!$checkIn || !$checkOut) {
            return redirect()->back()->with('error', 'Stay dates are missing.');
        }

        $roomRequirements = array_map(fn($item) => [
            'id' => $item['id'],
            'quantity' => $item['quantity']
        ], $stay['items']);

        $availabilityData = RoomsFindService::loadRequiredRooms($roomRequirements, $checkIn, $checkOut);
        // return $availabilityData;

        if ($availabilityData->isEmpty()) {
            if ($request->filled('check_in')) {
                return back()->with('error', 'Rooms not available for new dates. Keeping previous selection.');
            }
            return back()->with('error', 'Selected rooms are no longer available.');
        }

        if ($request->filled('check_in')) {
            session(['booking_check_in' => $checkIn, 'booking_check_out' => $checkOut]);
            session()->put('changedStay', true);
        }

        $availableRooms = $availabilityData['rooms'];
        $hotel = $availabilityData['hotel'];
        $nights = \Carbon\Carbon::parse($checkIn)->diffInDays($checkOut) ?: 1;

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
                return back()->with($validatedDiscount);
            }
        } else {
            $validatedDiscount = [
                'coupon_id' => null,
                'coupon_code' => null,
                'discount_amount' => 0,
                'final_amount' => round($rawTotal, 2),
                'final_converted_amount' => round($rawTotal * $exchangeRate, 2)
            ];
        }

        $checkoutPayload = [
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'nights' => $nights,
            'currency_symbol' => $userCountry['currency_symbol'],
            'hotelCurrencySymbol' => $hotelCountry->currency_symbol,
            'country_id' => $hotelCountry->id,
            'userCountryId' => $userCountry['country_id'],
            'converted' => $userCountry['currency_code'] != $hotelCountry->currency_code,

            'hotel' => [
                'id' => $hotel->id,
                'name' => $hotel->name,
                'city' => $hotel->city->name,
                'state' => $hotel->city->state->name,
                'address' => $hotel->address,
                'pincode' => $hotel->pincode,
            ],

            'rooms' => $availableRooms->map(function ($room) use ($exchangeRate) {
                return [
                    'id' => $room->details->id,
                    'title' => $room->details->title,
                    'path' => $room->details->images->first()->path ?? 'default.jpg',
                    'price' => round($room->details->price * $exchangeRate, 2),
                ];
            }),

            'finalActualTotal' => $rawTotal,
            'subTotal' => round($rawTotal * $exchangeRate, 2),
            'finalTotal' => $validatedDiscount['final_converted_amount'],
            'discountId' => $validatedDiscount['coupon_id'] ?? null,
            'discountCode' => $validatedDiscount['coupon_code'] ?? null,
            'discountAmount' => round($validatedDiscount['discount_amount'] * $exchangeRate, 2) ?? null,
        ];

        session()->put('checkoutPayload', $checkoutPayload);

        return view('client.checkout', compact('checkoutPayload', 'coupon_code'));
    }

    public function updateStayDates(Request $request)
    {
        $stay = session()->get('stay');
        $checkIn = $request->check_in;
        $checkOut = $request->check_out;
        $userCountry = LocationService::fetchLocation();

        if (!$stay || !$checkIn || !$checkOut) {
            return response()->json(['status' => false, 'message' => 'Invalid request data.']);
        }

        $roomRequirements = array_map(fn($item) => [
            'id' => $item['id'],
            'quantity' => $item['quantity']
        ], $stay['items']);

        $availability = RoomsFindService::roomsAvailability($roomRequirements, $checkIn, $checkOut);

        if ($availability->contains('available', false)) {
            return response()->json([
                'status' => false,
                'message' => 'Required rooms are not available for the selected Dates, Try Other Dates'
            ]);
        }

        session(['booking_check_in' => $checkIn, 'booking_check_out' => $checkOut]);

        $nights = \Carbon\Carbon::parse($checkIn)->diffInDays($checkOut) ?: 1;

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

        $response = [
            'status' => true,
            'nights' => $nights . ' ' . Str::plural('Night', $nights),
            'subtotal' => $stay['currency_symbol'] . ' ' . number_format($totals['subtotal'] * $nights, 2),
            'discount' => $stay['currency_symbol'] . ' ' . number_format(($totals['subtotal'] - $totals['grandTotal']) * $nights, 2),
            'grandTotal' => $stay['currency_symbol'] . ' ' . number_format($totals['grandTotal'] * $nights, 2),
            'date_display' => \Carbon\Carbon::parse($checkIn)->format('d M') . ' — ' . \Carbon\Carbon::parse($checkOut)->format('d M, Y')
        ];

        if (isset($stay['last_discount_error'])) {
            $response['message'] = $stay['last_discount_error'];
            unset($stay['last_discount_error']);
            session()->put('stay', $stay);
        }

        return response()->json($response);
    }

    private function syncStayCalculations(&$stay, $nights, $hotelId, $userCountry, $exchangeRate)
    {
        $rawSubtotal = collect($stay['items'])->sum(function ($item) {
            return $item['base_price'] * $item['quantity'];
        });

        $discountCode = null;
        if (!empty($stay['temp_discount_id'])) {
            $discount = Discount::find($stay['temp_discount_id']);
            $discountCode = $discount ? $discount->coupen_code : null;
        } elseif (!empty($stay['discount_id'])) {
            $discount = Discount::find($stay['discount_id']);
            $discountCode = $discount ? $discount->coupen_code : null;
        }

        $discountRatio = 0;

        if ($discountCode && $rawSubtotal > 0) {
            $validated = DiscountCoupenService::getDiscountPreview(
                $rawSubtotal,
                $discountCode,
                $nights,
                $hotelId,
                $userCountry['country_id'],
                $exchangeRate
            );

            if (isset($validated['status']) && $validated['status'] === true) {
                $discountRatio = $validated['discount_amount'] / $rawSubtotal;

                $stay['discount_id'] = $validated['coupon_id'];
                $stay['offer_message'] = Discount::find($validated['coupon_id'])->message ?? null;

                unset($stay['temp_discount_id']);
                unset($stay['last_discount_error']);
            } else {
                if (!empty($stay['discount_id'])) {
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
