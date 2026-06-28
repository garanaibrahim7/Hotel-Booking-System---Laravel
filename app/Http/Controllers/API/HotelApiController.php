<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Discount;
use App\Models\Hotel;
use App\Services\LocationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HotelApiController extends Controller
{
    public function index(Request $request)
    {
        $userCountry = LocationService::fetchLocation();
        $today = now();

        $checkIn = $request->check_in ?? session('booking_check_in');
        $checkOut = $request->check_out ?? session('booking_check_out');
        $nights = ($checkIn && $checkOut) ? Carbon::parse($checkIn)->diffInDays($checkOut) : 1;

        $query = Hotel::with([
            'city.state.country:id,name,currency_symbol,currency_code,iso_code',
            'images',
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

        $hotels = $query->paginate(10)->withQueryString()->through(function ($hotel) use ($userCountry, $exchangeRate, $today, $nights) {

            $hotel->rooms = $hotel->rooms->map(function ($room) use ($userCountry, $exchangeRate) {
                return (object) [
                    'id' => $room->id,
                    'price' => round($room->price * $exchangeRate, 2),
                    'currency_code' => $userCountry['currency_code'],
                    'currency_symbol' => $userCountry['currency_symbol'],
                    'type' => $room->type,
                    'category' => $room->category,
                ];
            });

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
                ->where('min_nights', '<=', $nights)
                ->orderByRaw('hotel_id IS NULL ASC')
                ->orderByRaw('country_id IS NULL ASC')
                ->get();

            $minRoomPrice = $hotel->rooms->min('price');

            $bestDiscount = null;
            $bestOfferPrice = $minRoomPrice;

            foreach ($discounts as $discount) {
                if ($discount->type === 'fixed') {
                    if ($discount->country_id && $discount->country_id != $userCountry['country_id']) {
                        continue;
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
                    $offerPrice = $minRoomPrice - $discount->value;
                }

                if ($offerPrice < $bestOfferPrice) {
                    $bestOfferPrice = $offerPrice;
                    $bestDiscount = $discount;
                }
            }

            $activeOffer = null;
            if ($bestDiscount && $bestOfferPrice < $minRoomPrice) {
                $activeOffer = [
                    'type' => $bestDiscount->type,
                    'value' => $bestDiscount->value,
                    'message' => $bestDiscount->message,
                ];
            }

            return [
                'id' => $hotel->id,
                'name' => $hotel->name,
                'description' => $hotel->description,
                'address' => $hotel->address,
                'cover_image' => $hotel->cover_image,
                'city' => $hotel->city->name ?? null,
                'state' => $hotel->city->state->name ?? null,
                'country' => $hotel->city->state->country->name ?? null,
                'reviews_avg_rating' => $hotel->reviews_avg_rating ? round($hotel->reviews_avg_rating, 1) : null,
                'reviews_count' => $hotel->reviews_count,
                'min_price' => round($minRoomPrice, 2),
                'discounted_min_price' => ($bestDiscount && $bestOfferPrice < $minRoomPrice) ? round(max(0, $bestOfferPrice), 2) : round($minRoomPrice, 2),
                'currency_symbol' => $userCountry['currency_symbol'],
                'active_offer' => $activeOffer,
                'room_types' => $hotel->rooms->pluck('type')->unique()->values(),
                'room_categories' => $hotel->rooms->pluck('category')->unique()->values(),
            ];
        });

        $cities = Cache::remember('all_cities_api_list', 86400, function () {
            return City::get()->map(function ($city) {
                return [
                    'id' => $city->id,
                    'full_name' => "{$city->location_details->city} - {$city->location_details->state} ({$city->location_details->country})",
                ];
            });
        });

        return response()->json([
            'success' => true,
            'data' => $hotels->items(),
            'meta' => [
                'current_page' => $hotels->currentPage(),
                'last_page' => $hotels->lastPage(),
                'per_page' => $hotels->perPage(),
                'total' => $hotels->total(),
            ],
            'filters' => [
                'cities' => $cities,
                'types' => ['Single', 'Double', 'Family', 'Twin'],
                'categories' => ['Standard', 'Suite', 'Deluxe', 'Premium', 'Luxury'],
            ],
            'selected' => $request->city_id
                ? ($hotels->first()['city'] ?? 'Selected City')
                : ($userCountry['country_name'] ?? 'Your Country'),
        ]);
    }
}
