<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoomExploreResource;
use App\Models\City;
use App\Services\LocationService;
use App\Services\RoomsFindService;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoomApiController extends Controller
{
    use HttpResponses;

    public function index(Request $request)
    {
        // sleep(2);
        $userCountry = LocationService::fetchLocation();

        $request->validate([
            'city_id' => 'nullable|integer',
            'hotel_id' => 'nullable|integer',
            'check_in' => 'nullable|date|after_or_equal:today',
            'check_out' => 'nullable|date|after:check_in',
            'adults' => 'nullable|integer|min:1',
            'children' => 'nullable|integer|min:0',
        ]);

        $page = $request->page ?? 1;
        $perPage = 15;

        $checkIn = $request->filled('check_in') ? $request->check_in : session('booking_check_in', today());
        $checkOut = $request->filled('check_out') ? $request->check_out : session('booking_check_out', today()->addDay());

        $paginatedHotels = RoomsFindService::loadAvailableRoomsPaginate(
            $checkIn,
            $checkOut,
            $request->hotel_id ?? null,
            $request->city_id ?? null,
            $userCountry,
            $request->adults ?? 1,
            $request->children ?? 0,
            $page,
            $perPage
        );

        // Log::channel('debug')->info('Total : '.$paginatedHotels->total());
        $message = $paginatedHotels->isEmpty() ? 'No Rooms Found for the Request' : 'Rooms Fetched Successfully';
        // Log::channel('debug')->info('Paginated Fetched Record: ', compact('paginatedHotels'));
        // Log::channel('debug')->info('Total: '. $paginatedHotels->total() ?? 0);

        // if($paginatedHotels->total() === 0){
        //     $city =
        // }
        return RoomExploreResource::collection($paginatedHotels)->additional([
            // 'meta' => [
            //     'total' => $paginatedHotels->total(),
            //     'current_page' => $paginatedHotels->currentPage(),
            //     'per_page' => $paginatedHotels->perPage(),
            //     'last_page' => $paginatedHotels->lastPage(),
            // ],
            'selected' => $request->city_id ? $paginatedHotels->first()?->hotel?->city_name : $userCountry['country_name'],
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'filters' => [
                'types' => ['Single', 'Double', 'Family', 'Twin'],
                'categories' => ['Standard', 'Suite', 'Deluxe', 'Premium', 'Luxury'],
                'cities' => City::get()->map(function ($city) {
                    return (object) [
                        'id' => $city->id,
                        'full_name' => "{$city->location_details->city} - {$city->location_details->state} ({$city->location_details->country})",
                    ];
                }),
                'values' => [
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                ],
            ],
        ])->response()->setStatusCode(200);

        return $this->success([
            'meta' => [
                'total' => $paginatedHotels->total(),
                'current_page' => $paginatedHotels->currentPage(),
                'per_page' => $paginatedHotels->perPage(),
                'last_page' => $paginatedHotels->lastPage(),
            ],
            'data' => RoomExploreResource::collection($paginatedHotels),
            'filters' => [
                'types' => ['Single', 'Double', 'Family', 'Twin'],
                'categories' => ['Standard', 'Suite', 'Deluxe', 'Premium', 'Luxury'],
            ],
        ], $message);
    }
}
