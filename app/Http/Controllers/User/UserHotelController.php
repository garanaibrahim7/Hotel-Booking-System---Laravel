<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Discount;
use App\Models\Hotel;
use App\Services\LocationService;
use App\Services\RoomsFindService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserHotelController extends Controller
{
    public function index(Request $request)
    {
        $userCountry = LocationService::fetchLocation();
        $today = now();

        // 1. Calculate nights exactly like the Service does
        $checkIn = $request->check_in ?? session('booking_check_in');
        $checkOut = $request->check_out ?? session('booking_check_out');
        $nights = ($checkIn && $checkOut) ? Carbon::parse($checkIn)->diffInDays($checkOut) : 1;


        $query = Hotel::with([
            'city.state.country:id,name,currency_symbol,currency_code',
            'rooms' => function ($q) {
                $q->select('id', 'hotel_id', 'price', 'type', 'category');
            },
        ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->has('rooms');

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
            $targetCity = City::findOrFail($request->city_id);
            $hotelCountry = $targetCity->state->country;
        } else {
            $query->whereHas('city.state.country', function ($q) use ($userCountry) {
                $q->where('iso_code', $userCountry['country_code']);
            });
            $hotelCountry = (object) $userCountry;
        }

        if ($request->anyFilled(['room_type', 'room_category'])) {
            $query->whereHas('rooms', function ($q) use ($request) {
                if ($request->filled('room_type')) {
                    $q->whereIn('type', (array) $request->room_type);
                }

                if ($request->filled('room_category')) {
                    $q->whereIn('category', (array) $request->room_category);
                }
            });
        }

        $exchangeRate = currencyExchangeRate(
            $userCountry['currency_code'],
            $hotelCountry->currency_code
        );

        // 2. Pass $nights into the closure
        $hotels = $query->paginate(10)->withQueryString()->through(function ($hotel) use ($userCountry, $exchangeRate, $today, $nights) {

            $hotel->rooms = $hotel->rooms->map(function ($room) use ($userCountry, $exchangeRate) {
                return (object) [
                    'id' => $room->id,
                    'price' => round($room->price * $exchangeRate, 2), // Price is converted here
                    'currency_code' => $userCountry['currency_code'],
                    'currency_symbol' => $userCountry['currency_symbol'],
                    'type' => $room->type,
                    'category' => $room->category,
                ];
            });

            // 3. Mirror the exact Discount query from RoomsFindService
            $discounts = Discount::where('active_status', true)
                ->where('required_code', false)
                ->where('starts_from', '<=', $today)
                ->where(function ($q) use ($today) {
                    $q->where('ends_at', '>=', $today)->orWhereNull('ends_at');
                })
                ->where(function ($q) use ($userCountry) {
                    $q->where('country_id', $userCountry['country_id'])
                        ->orWhereNull('country_id');
                })
                ->where(function ($q) use ($hotel) {
                    $q->where('hotel_id', $hotel->id)
                        ->orWhereNull('hotel_id');
                })
                ->where('min_nights', '<=', $nights) // Fix: Added min_nights filter
                ->orderByRaw('hotel_id IS NULL ASC') // Fix: Hotel-specific first
                ->orderByRaw('country_id IS NULL ASC') // Fix: Country-specific next
                ->get();

            // Since the map above already converted the price, min() gets the converted base price
            $minRoomPrice = $hotel->rooms->min('price');

            $bestDiscount = null;
            $bestOfferPrice = $minRoomPrice; // Default to base price

            // 4. Calculate logic matched exactly to the Service
            foreach ($discounts as $discount) {
                if ($discount->type === 'fixed') {
                    if ($discount->country_id && $discount->country_id != $userCountry['country_id']) {
                        continue; // Skip invalid fixed country discounts
                    }
                }

                $offerPrice = $minRoomPrice;

                if ($discount->type === 'percentage') {
                    $discountAmount = ($minRoomPrice * $discount->value) / 100;

                    if ($discount->max_discount) {
                        $discountAmount = min($discountAmount, $discount->max_discount);
                    }

                    $offerPrice = $minRoomPrice - $discountAmount;
                } else {
                    // Fixed calculation matched to how Service handles it on line 171
                    $offerPrice = $minRoomPrice - $discount->value;
                }

                // Find the lowest possible price (best discount)
                if ($offerPrice < $bestOfferPrice) {
                    $bestOfferPrice = $offerPrice;
                    $bestDiscount = $discount;
                }
            }

            $hotel->min_price = round($minRoomPrice, 2);
            $hotel->currency_symbol = $userCountry['currency_symbol'];

            if ($bestDiscount && $bestOfferPrice < $minRoomPrice) {
                $hotel->discounted_min_price = round(max(0, $bestOfferPrice), 2);
                $hotel->active_offer = $bestDiscount;

                $hotel->active_offer->discount_value =
                    $bestDiscount->type === 'percentage'
                    ? $bestDiscount->value
                    : round($minRoomPrice - $bestOfferPrice, 2); // Show the actual amount saved
            } else {
                $hotel->discounted_min_price = $hotel->min_price;
                $hotel->active_offer = null;
            }

            return $hotel;
        });

        $cities = Cache::remember('all_cities_list', 86400, function () {
            return City::get()->map(function ($city) {
                return (object) [
                    'id' => $city->id,
                    'full_name' => "{$city->location_details->city} - {$city->location_details->state} ({$city->location_details->country})",
                ];
            });
        });

        $types = ['Single', 'Double', 'Family', 'Twin'];
        $categories = ['Standard', 'Suite', 'Deluxe', 'Premium', 'Luxury'];

        return view('client.hotelExplore', compact('hotels', 'cities', 'types', 'categories'));
    }

    public function show($id, Request $request)
    {
        if ($request->filled('check_in') && $request->filled('check_out')) {
            session(['booking_check_in' => $request->check_in]);
            session(['booking_check_out' => $request->check_out]);
        }

        $checkIn = session('booking_check_in');
        $checkOut = session('booking_check_out');

        $userCountry = LocationService::fetchLocation();

        $hotelRooms = RoomsFindService::loadAvailableRooms(
            $checkIn,
            $checkOut,
            $id,
            null,
            $userCountry
        );

        // return $hotelRooms;
        $hotelData = Hotel::with('reviews')
            ->withAvg('reviews', 'rating')
            ->withAvg('reviews', 'cleaning')
            ->withAvg('reviews', 'services')
            ->withAvg('reviews', 'food')
            ->withAvg('reviews', 'hospitality')
            ->withCount('reviews')
            ->findOrFail($id);

        // return $hotelData;

        if (! $hotelData) {
            return redirect()->back()->with('error', 'No rooms available for these dates.');
        }

        return view('client.hotelRoomsExplore', [
            'hotel' => $hotelData,
            'rooms' => $hotelRooms->first()->rooms ?? collect(),
            'offer' => $hotelRooms->first()->offer ?? collect(),
            'userCountry' => $userCountry,
        ]);
    }
}
