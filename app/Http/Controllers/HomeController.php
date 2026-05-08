<?php

namespace App\Http\Controllers;

use App\Models\RoomDetail;
use App\Models\City;
use App\Models\Country;
use App\Services\LocationService;
use App\Services\RoomsFindService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function index()
    {
        $userCountry = LocationService::fetchLocation();
        $userCityId = $userCountry['city_id'] ?? null;
        $today = now();


        $localSpecialRooms = RoomsFindService::pricingAndOffersRooms($userCountry);

        $cacheKey = 'featured_rooms_top_10_' . $userCountry['country_code'];
        $featuredRooms = Cache::remember($cacheKey, 86400, function () use ($userCountry) {
            return RoomDetail::query()
                ->with(['hotel.city.state.country', 'images'])
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
            return (object)[
                'id' => $city->id,
                'name' => $city->name,
                'full_name' => "{$city->name} - {$city->state->name} ({$city->state->country->name})"
            ];
        });

        $hotelCities = City::whereHas('hotels.rooms')
            // ->withCount(['hotels as rooms_count' => function ($query) {
            //     $query->join('room_details', 'hotels.id', '=', 'room_details.hotel_id')
            //         ->select(\DB::raw('count(room_details.id)'));
            // }])
            // ->orderBy('rooms_count', 'desc')
            ->get()
            ->map(function ($city) {
                return (object)[
                    'id' => $city->id,
                    'name' => $city->name,
                ];
            });
        // $countries = Country::whereHas('states.cities.hotels')->get();

        Session::put('user_location', $userCountry);

        return view('client.home', compact('featuredRooms', 'localSpecialRooms', 'cities', 'hotelCities'));
    }

    public function roomsExplore(Request $request)
    {
        $query = RoomDetail::with(['images', 'hotel.city.state.country']);

        // Filters logic
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('collections')) {
            // Assuming your 'collections' map to a specific column or feature
            $query->whereHas('hotel', function ($q) use ($request) {
                $q->whereIn('type', $request->collections);
            });
        }

        $rooms = $query->paginate(10);
        $cities = City::all();

        return view('client.roomExplore', compact('rooms', 'cities'));
    }
}
