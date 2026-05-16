<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoomExploreResource;
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
        sleep(1);
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
        $perPage = 10;

        $paginatedHotels = RoomsFindService::loadAvailableRoomsPaginate(
            $request->filled('check_in') ? $request->check_in : today(),
            $request->filled('check_out') ? $request->check_out : today()->addDay(),
            $request->hotel_id ?? null,
            $request->city_id ?? null,
            $userCountry,
            $request->adults ?? 1,
            $request->children ?? 0,
            $page,
            $perPage
        );

        // Log::channel('debug')->info();
        $message = $paginatedHotels->isEmpty() ? 'No Rooms Found for the Request' : 'Rooms Fetched Successfully';

        return RoomExploreResource::collection($paginatedHotels)->additional([
             'meta' => [
                'total' => $paginatedHotels->total(),
                'current_page' => $paginatedHotels->currentPage(),
                'per_page' => $paginatedHotels->perPage(),
                'last_page' => $paginatedHotels->lastPage(),
            ],
            'filters' => [
                'types' => ['Single', 'Double', 'Family', 'Twin'],
                'categories' => ['Standard', 'Suite', 'Deluxe', 'Premium', 'Luxury'],
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
