<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookingItem;
use App\Models\City;
use App\Models\Discount;
use App\Models\Hotel;
use App\Models\RoomBlock;
use App\Models\RoomDetail;
use App\Services\LocationService;
use App\Services\RoomsFindService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserRoomController extends Controller
{
    public function index(Request $request)
    {
        $userCountry = LocationService::fetchLocation();

        $cityId = $request->city_id ?? session('user_location.city_id') ?? $userCountry['city_id'];
        $checkIn = $request->check_in ?? session('booking_check_in');
        $checkOut = $request->check_out ?? session('booking_check_out');

        if ($request->filled('check_in')) {
            $checkIn = Carbon::parse($request->check_in);
            if ($request->filled('check_out')) {
                $checkOut = Carbon::parse($request->check_out);
                if ($checkOut->lte($checkIn)) {
                    return back()->withErrors([
                        'check_out' => 'Check-out must be after check-in.',
                    ]);
                }
                session(['booking_check_out' => $request->check_out]);
            }
            session(['booking_check_in' => $request->check_in]);
        }
        if ($cityId != session('user_location.city_id')) {
            session(['user_location.city_id' => $cityId]);
        }

        $hotelsWithRooms = RoomsFindService::loadAvailableRooms(
            $checkIn,
            $checkOut,
            null,
            $cityId,
            $userCountry,
            $request->adults,
            $request->children
        );

        $page = Paginator::resolveCurrentPage() ?: 1;
        $perPage = 10;
        $paginatedHotels = new LengthAwarePaginator(
            $hotelsWithRooms->forPage($page, $perPage),
            $hotelsWithRooms->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );
        // return $hotelsWithRooms;

        $cities = Cache::remember('all_cities_list', 86400, function () {
            return City::get()->map(function ($city) {
                return (object) [
                    'id' => $city->id,
                    'full_name' => "{$city->location_details->city} - {$city->location_details->state} ({$city->location_details->country})",
                ];
            });
        });

        // return $cities;

        $types = ['Single', 'Double', 'Family', 'Twin'];
        $categories = ['Standard', 'Suite', 'Deluxe', 'Premium', 'Luxury'];

        return view('client.roomExplore', [
            'hotelsWithRooms' => $paginatedHotels,
            'userCountry' => $userCountry,
            'cities' => $cities,
            'types' => $types,
            'categories' => $categories,
        ]);
    }

    public function show($id)
    {
        $userLocation = session('user_location');
        $userCountry = LocationService::fetchLocation();
        $room = RoomDetail::with(['hotel.city.state.country', 'images'])
            ->findOrFail($id);

        $hotel = Hotel::with('reviews')
            ->withAvg('reviews', 'rating')
            ->withAvg('reviews', 'cleaning')
            ->withAvg('reviews', 'services')
            ->withAvg('reviews', 'food')
            ->withAvg('reviews', 'hospitality')
            ->withCount('reviews')
            ->findOrFail($room->hotel_id);

        $hotelCurrency = $hotel->city->state->country->currency_code;

        $exchangeRate = currencyExchangeRate($userLocation['currency_code'] ?? $userCountry['currency_code'], $hotelCurrency);

        $today = now();
        $nights = Carbon::parse(session('booking_check_in'))->diffInDays(session('booking_check_out')) ?: 1;

        $discount = Discount::where('active_status', true)
            ->where('required_code', false)
            ->where('starts_from', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->where('ends_at', '>=', $today)->orWhereNull('ends_at');
            })
            ->where(function ($q) use ($hotel) {
                $q->where('hotel_id', $hotel->id)->orWhereNull('hotel_id');
            })
            ->where('min_nights', '<=', $nights)
            ->orderBy('hotel_id', 'desc')
            ->first();

        $room->converted_price = round($room->price * $exchangeRate, 2);
        $room->user_currency_symbol = $userLocation['currency_symbol'] ?? $userCountry['currency_code'];
        $room->offer_price = null;

        if ($discount) {
            $rawDiscounted = ($discount->type == 'percentage')
                ? $room->price - ($room->price * $discount->value / 100)
                : $room->price - $discount->value;

            $room->offer_price = round($rawDiscounted * $exchangeRate, 2);
            $room->offer_message = $discount->message;
        }

        $similarRooms = RoomDetail::where('hotel_id', $hotel->id)
            ->where('id', '!=', $id)
            ->take(3)
            ->get();

        return view('client.room.explore-room', compact('room', 'similarRooms', 'hotel'));
    }

    public function getRoomAvailability(Request $request, $id)
    {
        try {
            $month = $request->input('month', now()->month);
            $year = $request->input('year', now()->year);

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            // 1. FIXED: Only fetch physical rooms where status is true (1)
            $roomDetail = RoomDetail::with(['rooms' => function ($query) {
                $query->where('status', 1); // or true, depending on your DB column type
            }])->findOrFail($id);

            $totalPhysicalRooms = $roomDetail->rooms->count();
            $roomIds = $roomDetail->rooms->pluck('id');

            // If there are no active rooms at all, return early to save processing
            if ($totalPhysicalRooms === 0) {
                return response()->json([
                    'date_states' => [],
                    'user_bookings' => [],
                    'total_inventory' => 0,
                ]);
            }

            // 2. FIXED: Use proper overlap logic instead of whereBetween
            $bookings = BookingItem::whereIn('room_id', $roomIds)
                ->whereHas('booking', function ($q) {
                    $q->where('status', 1);
                })
                ->where('check_in', '<=', $endDate)
                ->where('check_out', '>=', $startDate)
                ->get(['check_in', 'check_out', 'booking_id']);

            // 3. FIXED: Use proper overlap logic for blocks
            $blocks = RoomBlock::where('room_detail_id', $id)
                ->where('from', '<=', $endDate)
                ->where('to', '>=', $startDate)
                ->get(['from', 'to', 'reason']);

            $userBookings = BookingItem::whereIn('room_id', $roomIds)
                ->whereHas('booking', function ($q) {
                    $q->where('user_id', \Auth::id())
                        ->whereIn('status', [1, 3]); // Confirmed or Processing
                })
                ->get(['check_in', 'check_out'])
                ->map(function ($b) {
                    return [
                        'start' => Carbon::parse($b->check_in)->format('Y-m-d'),
                        'end' => Carbon::parse($b->check_out)->format('Y-m-d'),
                    ];
                });

            $dateStates = [];
            $period = CarbonPeriod::create($startDate, $endDate);

            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');

                $occupancyCount = $bookings->filter(function ($b) use ($date) {
                    $checkIn = Carbon::parse($b->check_in)->startOfDay();
                    $checkOut = Carbon::parse($b->check_out)->startOfDay();

                    return $date->betweenIncluded($checkIn, $checkOut->copy()->subDay());
                })->count();

                $isBlocked = $blocks->first(function ($block) use ($date) {
                    $blockFrom = Carbon::parse($block->from)->startOfDay();
                    $blockTo = Carbon::parse($block->to)->endOfDay();

                    return $date->betweenIncluded($blockFrom, $blockTo);
                });

                $status = 'available';
                $reason = null;

                if ($isBlocked) {
                    $status = 'blocked';
                    $reason = $isBlocked->reason ?? 'Maintenance';
                } elseif ($occupancyCount >= $totalPhysicalRooms) {
                    $status = 'sold_out';
                } elseif ($occupancyCount > 0) {
                    $status = 'limited';
                }

                $dateStates[$dateStr] = [
                    'status' => $status,
                    'rooms_left' => max(0, $totalPhysicalRooms - $occupancyCount),
                    'reason' => $reason,
                ];
            }

            return response()->json([
                'date_states' => $dateStates,
                'user_bookings' => $userBookings,
                'total_inventory' => $totalPhysicalRooms,
            ]);

        } catch (\Exception $e) {
            Log::error('Calendar API Error: '.$e->getMessage().' on line '.$e->getLine());

            return response()->json([
                'error_message' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
            ], 500);
        }
    }
}
