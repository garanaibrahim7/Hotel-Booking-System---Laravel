<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\RoomDetail;
use App\Services\LocationService;
use App\Services\RoomsFindService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeApiController extends Controller
{
    public function getFeaturedRooms()
    {
        $userCountry = LocationService::fetchLocation();
        $cacheKey = 'featured_rooms_top_10_'.$userCountry['country_code'];

        $featuredRooms = Cache::remember($cacheKey, 86400, function () use ($userCountry) {
            return RoomDetail::query()
                ->with(['images', 'hotel.city.state.country'])
                ->whereHas('hotel.city.state.country', function ($q) use ($userCountry) {
                    $q->where('iso_code', $userCountry['country_code']);
                })
                ->withCount(['rooms as bookings_count' => function ($q) {
                    $q->whereHas('bookingItems');
                }])
                ->selectSub(function ($query) {
                    $query->from('reviews')
                        ->selectRaw('count(*)')
                        ->whereColumn('hotel_id', 'room_details.hotel_id');
                }, 'hotel_reviews_count')
                ->addSelect('room_details.*')
                ->orderByRaw('(bookings_count + hotel_reviews_count) DESC')
                ->take(10)
                ->get();
        });

        return response()->json([
            'success' => true,
            'data' => $featuredRooms,
        ], 200);
    }

    public function getSpecialOffers()
    {
        $userCountry = LocationService::fetchLocation();

        $localSpecialRooms = RoomsFindService::pricingAndOffersRooms($userCountry);

        return response()->json([
            'success' => true,
            'data' => $localSpecialRooms,
        ], 200);
    }

    public function heroImage() {
        return response()->json([
            'success' => true,
            'data' => asset('/storage/assets/banner.jpg'),
        ], 200);
    }

    public function index()
    {
        $userCountry = LocationService::fetchLocation();

        $localSpecialRooms = RoomsFindService::pricingAndOffersRooms($userCountry);

        $cacheKey = 'featured_rooms_top_10_'.$userCountry['country_code'];
        $featuredRooms = Cache::remember($cacheKey, 86400, function () use ($userCountry) {
            return RoomDetail::query()
                ->with(['images', 'hotel.city.state.country'])
                ->whereHas('hotel.city.state.country', function ($q) use ($userCountry) {
                    $q->where('iso_code', $userCountry['country_code']);
                })
                ->withCount(['rooms as bookings_count' => function ($q) {
                    $q->whereHas('bookingItems');
                }])
                ->selectSub(function ($query) {
                    $query->from('reviews')
                        ->selectRaw('count(*)')
                        ->whereColumn('hotel_id', 'room_details.hotel_id');
                }, 'hotel_reviews_count')
                ->addSelect('room_details.*')
                ->orderByRaw('(bookings_count + hotel_reviews_count) DESC')
                ->take(10)
                ->get();
        });

        $cities = City::with('state.country')->get()->map(function ($city) {
            return [
                'id' => $city->id,
                'name' => $city->name,
                'full_name' => "{$city->name} - {$city->state->name} ({$city->state->country->name})",
            ];
        });

        $hotelCities = City::whereHas('hotels.rooms')
            ->get()
            ->map(function ($city) {
                return [
                    'id' => $city->id,
                    'name' => $city->name,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'banner_image' => asset('/storage/assets/banner.jpg'),
                'user_country' => $userCountry,
                'featured_rooms' => $featuredRooms,
                'local_special_rooms' => $localSpecialRooms,
                'search_cities' => $cities,
                'hotel_cities' => $hotelCities,
            ],
        ], 200);
    }

    public function roomsExplore(Request $request)
    {
        $query = RoomDetail::with(['images', 'hotel.city.state.country']);

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('collections')) {
            $query->whereHas('hotel', function ($q) use ($request) {
                $q->whereIn('type', $request->collections);
            });
        }

        $rooms = $query->paginate(10);
        $cities = City::all();

        return response()->json([
            'success' => true,
            'rooms' => $rooms,
            'cities' => $cities,
        ], 200);
    }
}
