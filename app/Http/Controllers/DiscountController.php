<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiscountFormRequest;
use App\Models\Booking;
use App\Models\Country;
use App\Models\Discount;
use App\Models\Hotel;
use App\Services\DiscountCoupenService;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\RedisPubSubHandler;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search');
        $discounts = Discount::where('coupen_code', 'like', "%{$search}%")
            ->paginate(15);
        return view('admin.discount.list', compact('discounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.discount.add', [
            'countries' => Country::get(['id', 'name', 'currency_code']),
            'hotels' => Hotel::get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DiscountFormRequest $request)
    {
        // return $request;
        // if ($request->has('required_code') && $request->required_code) {
        // return $request;
        // return 'Discount Add';
        $validated = $request->validated();
        if ($validated['type'] === 'fixed') {
            $validated['max_discount'] = $validated['value'];
        }
        // return $validated;
        Discount::create($validated);
        return redirect()->route('admin.discounts.index')->with('success', 'Discount Coupen Created Successfully');
        // }
        // elseif ($request->has('required_code') && !$request->required_code) {
        //     return $request;
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show(Discount $discount)
    {
        $userLocation = LocationService::fetchLocation();

        $usageLogs = $discount->bookings()
            ->where('status', Booking::STATUS_CONFIRMED)
            ->latest()
            ->paginate(10);
        $discount->availedAmount = $discount->bookings()
            ->where('status', 1)
            ->get()
            ->reduce(function ($carry, $booking) use ($userLocation) {
                $bookingCurrency = $booking->currency;
                $amount = $booking->discount_amount;

                if ($userLocation['currency_code'] !== $bookingCurrency) {
                    $amount = convertCurrency($amount, $userLocation['currency_code'], $bookingCurrency);
                }

                return $carry + $amount;
            }, 0);

        return view('admin.discount.usage', compact('discount', 'usageLogs'));
    }

    public function createSeasonalOffer()
    {
        $countries = Country::get(['id', 'name', 'currency_code']);
        $hotels = Hotel::get(['id', 'name']);
        // return view('admin.discount.seasonal-offer', compact('countries', 'hotels'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Discount $discount)
    {
        $countries = Country::select('id', 'name', 'currency_code')->get();
        return view('admin.discount.edit', compact('discount', 'countries'));
    }

    public function editForm(Request $request)
    {
        // return $request;
        $discount = Discount::findOrFail($request->input('id'));
        $countries = Country::select('id', 'name', 'currency_code')->get();
        return view('admin.discount.edit', compact('discount', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(DiscountFormRequest $request, Discount $discount)
    {
        // return $request;
        $validated = $request->validated();

        $discount->update($validated);

        return redirect()->route('admin.discounts.index')->with('success', 'Discount Coupon Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discount $discount)
    {
        return;
    }

    public function toggleActive(Discount $discount)
    {
        $status = $discount->active_status;
        $discount->update(['active_status' => !$status]);
        return redirect()->route('admin.discounts.index')->with('success', 'Coupen ' . $discount->coupen_code . ($status ? ' Deactivated ' : ' Re-Activated ') . 'Successfully');
    }


    public function validateCouponCode(Request $request)
    {
        // return $request;

        // sleep(1);

        $couponCode = $request->couponCode;
        $totalAmount = $request->totalAmount;
        $nights = $request->nights;
        $hotelId = $request->hotelId;
        $userCountryId = $request->userCountryId;

        $validatedCouponDetails = DiscountCoupenService::validateCoupen($totalAmount, $couponCode, $nights, $hotelId, $userCountryId);

        if (isset($validatedCouponDetails['error'])) {
            return response()->json($validatedCouponDetails);
        }
        if ($validatedCouponDetails['status']) {
            // Log::channel('debug')->info("Discount ----");

            $checkoutPayload = session()->get('checkoutPayload');
            // if ($checkoutPayload) {
            //     unset($checkoutPayload['discountId']);
            //     unset($checkoutPayload['discountCode']);
            //     unset($checkoutPayload['discountAmount']);
            //     $checkoutPayload['finalTotal'] = $checkoutPayload['subTotal'];

            //     session()->put('checkoutPayload', $checkoutPayload);
            // }

            $stay = session()->get('stay');

            if ($stay) {
                $stay['discount_id'] = $validatedCouponDetails['coupon_id'];
                $stay['offer_message'] = "Coupon Applied : " . $validatedCouponDetails['coupon_code'];

                session()->put('stay', $stay);
                session()->put('changedStay', true);
            }
            return response()->json($validatedCouponDetails);
        }
        return response()->json(['error' => 'Some Services are Not Working at Moment']);
    }

    public function removeCode(Request $request)
    {
        $stay = session()->get('stay', []);
        if ($stay) {
            unset($stay['discount_id']);
            unset($stay['offer_message']);

            session()->put('stay', $stay);
            session()->put('changedStay', true);
        }

        $checkoutPayload = session()->get('checkoutPayload');
        if ($checkoutPayload) {
            unset($checkoutPayload['discountId']);
            unset($checkoutPayload['discountCode']);
            unset($checkoutPayload['discountAmount']);
            $checkoutPayload['finalTotal'] = $checkoutPayload['subTotal'];

            session()->put('checkoutPayload', $checkoutPayload);
            // session()->put('changedStay', true);
        }


        return response()->json(['status' => true, 'message' => 'Coupon Removed Successfully']);
    }
}
