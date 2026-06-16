<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomFormRequest;
use App\Models\BookingItem;
use App\Models\Hotel;
use App\Models\Image;
use App\Models\Room;
use App\Models\RoomDetail;
use App\Services\LocationService;
use App\Services\RoomsAddService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function index()
    {
        abort_if(request()->missing('room_detail_id'), 400, 'Request Parameters are Missing');

        $roomDetailId = request('room_detail_id');
        $today = now()->toDateString();

        $roomDetail = RoomDetail::with(['hotel.city.state.country', 'images'])
            ->findOrFail($roomDetailId);

        $rooms = Room::where('room_detail_id', $roomDetailId)
            ->select(['id', 'room_detail_id', 'hotel_id', 'status', 'room_number'])
            ->withExists(['bookingItems as is_booked_today' => function ($query) use ($today) {
                $query->whereDate('check_in', '<=', $today)
                    ->whereDate('check_out', '>', $today)
                    ->whereHas('booking', function ($b) {
                        $b->where('status', 1);
                    });
            }])
            ->get();

        $userLocation = LocationService::fetchLocation();
        $hotelCountry = $roomDetail->hotel->city->state->country;

        // Currency Formatting Logic
        $convertedAmount = convertCurrency($roomDetail->price, $userLocation['currency_code'], $hotelCountry->currency_code);
        $roomDetail->converted_price = $userLocation['currency_symbol'].' '.number_format($convertedAmount, 2);
        $roomDetail->price = $hotelCountry->currency_symbol.' '.number_format($roomDetail->price, 2);

        // return $hotelCountry->currency_code . ' <-> ' . $userLocation['currency_code'];
        return view('admin.room.list', [
            'rooms' => $rooms,
            'details' => $roomDetail,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        session()->put('add_rooms_previous', url()->previous());

        if (request('category')) {
            $category = RoomDetail::select(['id', 'hotel_id', 'type', 'category'])
                ->with('hotel:name')
                ->findOrFail(request('category'))
                ->append('title');

            // return $category;
            return view('admin.room.add', compact('category'));
        }
        $categories = RoomDetail::select(['id', 'category', 'type', 'hotel_id'])->get()->append('title');
        // return $categories;
        $hotels = Hotel::select(['id', 'name'])->get();

        return view('admin.room.add', compact('categories', 'hotels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoomFormRequest $request, RoomsAddService $service)
    {
        $service->insertRooms($request->only([
            'room-add-option',
            'room_number',
            'room_number_from',
            'room_number_to',
            'room_number_prefix',
            'room_detail_id',
            'hotel_id',
            'status',
        ]), true);

        return redirect(session()->get('add_rooms_previous') ?? route('admin.categories.index'))->with('success', 'Rooms Added Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        return $room;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        // return $room;
        return view('admin.room.edit', compact('room'));
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(RoomFormRequest $request, Room $room)
    public function update(Request $request, Room $room)
    {
        // return $room->update(['status' => 0]);
        if ($request->replaceImages) {
            foreach ($request->file('replaceImages') as $key => $file) {
                $newPath = $file->store('assets/hotel', 'public');
                $image = Image::findOrFail($key);
                $this->deleteImage($image->path);
                $image->path = $newPath;
                $image->save();
            }
        }
        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $file) {

                $path = $file->store('/assets/room', 'public');
                $room->images()->create([
                    'path' => $path,
                ]);
            }
        }
        if ($request->deleteImages) {
            foreach ($request->deleteImages as $imageId) {
                $image = Image::findOrFail($imageId);
                $this->deleteImage($image->path);
                $image->delete();
            }
        }

        return view('admin.room.list');
    }

    private function deleteImage(string $imagePath)
    {
        Storage::disk('public')->delete($imagePath);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        // return $room;
        // Room::query()->delete();
        $room->delete();

        return back();
    }

    public function bookings($id)
    {
        $room = Room::with(['details', 'hotel'])->findOrFail($id);

        $today = now()->toDateString();

        $allBookings = BookingItem::where('room_id', $id)
            ->with(['booking.user', 'booking.payment'])
            ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            ->select('booking_items.*')
            ->orderBy('booking_items.check_in', 'desc')
            ->get();

        $upcoming = $allBookings->filter(function ($item) use ($today) {
            return $item->check_in >= $today && $item->booking->status == 1;
        });

        $past = $allBookings->filter(function ($item) use ($today) {
            return $item->check_out <= $today || $item->booking->status != 1;
        });

        // return compact('room','upcoming','past');

        return view('admin.room.bookings', compact('room', 'upcoming', 'past'));
    }

    public function changeRoomStatus(Room $room)
    {
        $room->status = $room->status ? 0 : 1;
        $room->save();

        return back()->with('success', 'Room Status has been Changed');
    }

    public function getCalendarData(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $roomDetailId = $request->input('room_detail_id');
        if ($roomDetailId === 'null' || $roomDetailId === '') {
            $roomDetailId = null;
        }

        $roomId = $request->input('room_id');
        if ($roomId === 'null' || $roomId === '') {
            $roomId = null;
        }

        if (! $start || ! $end) {
            $start = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end = Carbon::now()->endOfMonth()->addDay()->format('Y-m-d');
        }

        $events = [];

        // ==========================================
        // SCENARIO 1: Category Level (Multiple Rooms)
        // ==========================================
        if ($roomDetailId && ! $roomId) {

            $totalRooms = DB::table('rooms')->where([
                ['room_detail_id', $roomDetailId],
                ['status', true],
            ])->count();

            $blocks = DB::table('room_blocks')
                ->where('room_detail_id', $roomDetailId)
                ->where('to', '>=', $start)
                ->where('from', '<=', $end)
                ->get();

            $bookings = DB::table('booking_items')
                ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
                ->join('rooms', 'booking_items.room_id', '=', 'rooms.id')
                ->where('rooms.room_detail_id', $roomDetailId)
                ->whereNotIn('bookings.status', ['cancelled', 'refunded'])
                ->where('booking_items.check_out', '>', $start)
                ->where('booking_items.check_in', '<', $end)
                ->select('booking_items.id', 'booking_items.check_in', 'booking_items.check_out')
                ->get();

            $period = CarbonPeriod::create($start, Carbon::parse($end)->subDay());

            foreach ($period as $date) {
                $dateString = $date->format('Y-m-d');

                $dailyBlocks = $blocks->filter(function ($b) use ($dateString) {
                    $from = date('Y-m-d', strtotime($b->from));
                    $to = date('Y-m-d', strtotime($b->to));

                    return $dateString >= $from && $dateString <= $to;
                });

                $dailyBookings = $bookings->filter(function ($b) use ($dateString) {
                    $checkIn = date('Y-m-d', strtotime($b->check_in));
                    $checkOut = date('Y-m-d', strtotime($b->check_out));

                    return $dateString >= $checkIn && $dateString < $checkOut;
                });

                $blockedCount = $dailyBlocks->count();
                $bookingCount = $dailyBookings->count();
                $availableCount = $totalRooms - $blockedCount - $bookingCount;
                // Log::info('Date String: '.$dateString);
                if ($blockedCount > 0) {
                    $events[] = [
                        'start' => $dateString,
                        'title' => $blockedCount.' Blocked',
                        'color' => '#dc3545',
                        'display' => ($availableCount <= 0) ? 'block' : 'auto',
                        'extendedProps' => [
                            'type' => 'block',
                            'date' => $dateString,
                            'detail_id' => $roomDetailId,
                            'url' => url('/admin/room-blocks?date='.$dateString.'&room_detail_id='.$roomDetailId),
                        ],
                    ];
                }

                if ($bookingCount > 0) {
                    $events[] = [
                        'start' => $dateString,
                        'title' => $bookingCount.' Bookings',
                        'color' => '#0d6efd',
                        'extendedProps' => [
                            'type' => 'booking_list',
                            'date' => $dateString,
                            'detail_id' => $roomDetailId,
                            // ADDED URL: View bookings for this category on this date
                            'url' => url('/admin/bookings?date='.$dateString.'&room_detail_id='.$roomDetailId),
                        ],
                    ];
                }

                if ($dateString < now()->format('Y-m-d')) {
                    $events[] = [
                        'start' => $dateString,
                        'title' => max(0, $availableCount).' was Available',
                        'color' => 'transparent',
                        'textColor' => '#676767',
                        'extendedProps' => ['type' => 'info'],
                    ];
                } else {
                    $events[] = [
                        'start' => $dateString,
                        'title' => max(0, $availableCount).' Available',
                        'color' => 'transparent',
                        'textColor' => ($availableCount > 0) ? '#198754' : '#dc3545',
                        'extendedProps' => ['type' => 'info'],
                    ];
                }
            }
        }

        // ==========================================
        // SCENARIO 2: Specific Physical Room Level
        // ==========================================
        elseif (! $roomDetailId && $roomId) {

            $room = DB::table('rooms')->where('id', $roomId)->first();
            $parentDetailId = $room ? $room->room_detail_id : null;

            $blocks = DB::table('room_blocks')
                ->where('room_detail_id', $parentDetailId)
                ->where('to', '>=', $start)
                ->where('from', '<=', $end)
                ->get();

            $bookings = DB::table('booking_items')
                ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
                ->where('booking_items.room_id', $roomId)
                ->whereNotIn('bookings.status', ['cancelled', 'refunded'])
                ->where('booking_items.check_out', '>', $start)
                ->where('booking_items.check_in', '<', $end)
                ->select('booking_items.id', 'booking_items.check_in', 'booking_items.check_out', 'bookings.id as booking_id')
                ->get();

            $period = CarbonPeriod::create($start, Carbon::parse($end)->subDay());

            foreach ($period as $date) {
                $dateString = $date->format('Y-m-d');

                $dailyBlock = $blocks->first(function ($b) use ($dateString) {
                    $from = date('Y-m-d', strtotime($b->from));
                    $to = date('Y-m-d', strtotime($b->to));

                    return $dateString >= $from && $dateString <= $to;
                });

                $dailyBooking = $bookings->first(function ($b) use ($dateString) {
                    $checkIn = date('Y-m-d', strtotime($b->check_in));
                    $checkOut = date('Y-m-d', strtotime($b->check_out));

                    return $dateString >= $checkIn && $dateString < $checkOut;
                });

                if ($dailyBlock) {
                    $events[] = [
                        'start' => $dateString,
                        'title' => 'Category Blocked',
                        'color' => '#dc3545',
                        'display' => 'block',
                        'extendedProps' => [
                            'type' => 'block',
                            'id' => $dailyBlock->id,
                            'url' => url('/admin/room-blocks/'.$dailyBlock->id.'/edit'),
                        ],
                    ];
                }

                if ($dailyBooking) {
                    $events[] = [
                        'start' => $dateString,
                        'title' => 'Occupied',
                        'color' => '#fd7e14',
                        'display' => 'block',
                        'extendedProps' => [
                            'type' => 'single_booking',
                            'id' => $dailyBooking->booking_id,
                            // ADDED URL: View specific booking details
                            'url' => url('/admin/bookings/'.$dailyBooking->booking_id),
                        ],
                    ];
                }

                if (! $dailyBlock && ! $dailyBooking) {
                    if ($dateString < now()->format('Y-m-d')) {
                        $events[] = [
                            'start' => $dateString,
                            'title' => 'Was Available',
                            'color' => 'transparent',
                            'textColor' => '#676767',
                            'extendedProps' => ['type' => 'info'],
                        ];
                    } else {
                        $events[] = [
                            'start' => $dateString,
                            'title' => 'Available',
                            'color' => 'transparent',
                            'textColor' => '#198754',
                            'extendedProps' => ['type' => 'info'],
                        ];
                    }
                }
            }
        }

        return response()->json($events);
    }
}
