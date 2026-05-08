<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Hotel;
use App\Models\Review;
use App\Models\Room;
use App\Services\LocationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laracsv\Export;

class ManagerController extends Controller
{
    public function index()
    {

        $hotel = Hotel::where('user_id', Auth::id())->firstOrFail();
        $hotelId = $hotel->id;

        $managerLocation = LocationService::fetchLocation();
        $today = Carbon::today();

        $totalRooms = Room::where('hotel_id', $hotelId)->where('status', 1)->count();

        $todaysBookings = Booking::where('hotel_id', $hotelId)
            ->whereDate('created_at', $today)
            ->count();

        $totalBookings = Booking::where('hotel_id', $hotelId)->count();

        $totalRevenue = Booking::where('hotel_id', $hotelId)
            ->where('status', Booking::STATUS_CONFIRMED)
            ->get(['total_amount', 'currency'])
            ->reduce(function ($carry, $booking) use ($managerLocation) {
                return $carry + (convertCurrency($booking->total_amount, $managerLocation['currency_code'], $booking->currency));
            }, 0);

        $upcomingStays = BookingItem::whereHas('room', function ($q) use ($hotelId) {
            $q->where('hotel_id', $hotelId);
        })
            ->where('check_in', '>', now())
            ->count();

        $latestReviews = Review::where('hotel_id', $hotelId)
            ->with('user')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $currentYear = date('Y');
        $lastYear = $currentYear - 1;

        $getMonthCounts = function ($year) use ($hotelId) {
            return DB::table('bookings')
                ->select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
                ->where('hotel_id', $hotelId)
                ->whereYear('created_at', $year)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->all();
        };

        $currentYearRaw = $getMonthCounts($currentYear);
        $lastYearRaw = $getMonthCounts($lastYear);

        $currentYearData = [];
        $lastYearData = [];
        for ($i = 1; $i <= 12; $i++) {
            $currentYearData[] = $currentYearRaw[$i] ?? 0;
            $lastYearData[] = $lastYearRaw[$i] ?? 0;
        }

        $occupancyData = [];
        $occupancyLabels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $occupancyLabels[] = $date->format('D');

            $occupiedCount = BookingItem::whereHas('room', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            })
                ->whereDate('check_in', '<=', $date)
                ->whereDate('check_out', '>', $date)
                ->count();

            $percentage = $totalRooms > 0 ? ($occupiedCount / $totalRooms) * 100 : 0;
            $occupancyData[] = round(min($percentage, 100), 1);
        }

        return view('admin.dashboard', compact(
            'hotel',
            'totalRooms',
            'todaysBookings',
            'totalBookings',
            'totalRevenue',
            'upcomingStays',
            'latestReviews',
            'currentYearData',
            'lastYearData',
            'occupancyData',
            'occupancyLabels'
        ));
    }

    public function bookings()
    {
        $hotel = Hotel::where('user_id', Auth::id())->firstOrFail();
        $hotelId = $hotel->id;

        $bookingsOf = request('bookingsOf');
        $search = request('search');
        $date = request('date');
        $filter = request('filter', 'today');

        $totalBookings = $hotel->bookings()->where('status', Booking::STATUS_CONFIRMED)->count();
        $totalOtherBookings = $hotel->bookings()->where('status', '!=', Booking::STATUS_CONFIRMED)->count();

        $totalRevenue = $hotel->bookings
            ->where('status', Booking::STATUS_CONFIRMED)
            ->reduce(fn ($carry, $booking) => $carry + $booking->payment->amount);

        $upcomingStays = BookingItem::whereHas('room', function ($q) use ($hotelId) {
            $q->where('hotel_id', $hotelId);
        })
            ->where('check_in', '>', now())
            ->count();

        $query = BookingItem::with([
            'booking.user',
            'booking.payment',
            'room.details',
            'booking.hotel',
        ])
            ->join('bookings', function ($join) use ($hotelId) {
                $join->on('booking_items.booking_id', '=', 'bookings.id')
                    ->where('bookings.hotel_id', $hotelId);
            })
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->select('booking_items.*')
            ->orderBy('bookings.created_at', 'desc');

        if ($date) {
            $query->whereDate('bookings.created_at', $date);
        }

        if (! empty($bookingsOf) && $bookingsOf !== 'all') {
            $query->whereBetween('bookings.created_at', match ($bookingsOf) {
                'today' => [now()->startOfDay(), now()->endOfDay()],
                'week' => [now()->addDays(-7), now()->endOfDay()],
                'month' => [now()->startOfMonth(), now()->endOfMonth()],
                'year' => [now()->startOfYear(), now()->endOfYear()],
                default => [now()->startOfDay(), now()->endOfDay()],
            });
        } elseif (empty($bookingsOf) && ! $hotel) {
            $query->whereBetween('bookings.created_at', [now()->startOfDay(), now()->endOfDay()]);
        }
        // return $bookingsOf;

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('bookings.reference_number', 'like', "%{$search}%");
            });
        }

        $bookings = $query->paginate(10)->withQueryString();

        return view('admin.booking.list', compact('bookings', 'search', 'date', 'totalBookings', 'totalRevenue', 'totalOtherBookings', 'upcomingStays'));
    }

    public function show(Booking $booking)
    {
        return view('admin.booking.detail', compact('booking'));
    }

    public function printList()
    {
        $hotel = Hotel::where('user_id', Auth::id())->firstOrFail();
        $hotelId = $hotel->id;
        $userCountry = LocationService::fetchLocation();

        $userCountry = LocationService::fetchLocation();

        $bookingsOf = request('bookingsOf');
        $search = request('search');
        $date = request('date');
        $operation = request('operation', 'print');

        // session()->put('previous_of_bookings', url()->previous());

        $query = Booking::with([
            'payment',
            'items.room.details',
            'hotel',
        ])
            ->where('hotel_id', $hotelId)
            // ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            // ->join('users', 'bookings.user_id', '=', 'users.id')
            // ->select('booking_items.*')
            ->orderBy('bookings.created_at', 'desc');

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        if (! empty($bookingsOf) && $bookingsOf !== 'all') {
            // Log::channel('debug')->alert('Executed: '.$bookingsOf);
            $query->whereBetween('created_at', match ($bookingsOf) {
                'today' => [now()->startOfDay(), now()->endOfDay()],
                'week' => [now()->addDays(-7), now()->endOfDay()],
                'month' => [now()->startOfMonth(), now()->endOfMonth()],
                'year' => [now()->startOfYear(), now()->endOfYear()],
                default => [now()->startOfDay(), now()->endOfDay()],
            });
        } elseif (empty($bookingsOf) && ! $hotel) {
            $query->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                    ->orWhere('guest_email', 'like', "%{$search}%")
                    ->orWhereHas('hotel', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        $bookings = $query->get();

        // return $bookings;
        $operation = request('operation', 'print');
        $reportTitle = 'Booking Report - '.($date ?? ucfirst($bookingsOf ?? 'Today'));

        if ($operation == 'download-pdf') {
            $pdf = Pdf::loadView('admin.booking.print', compact('bookings', 'reportTitle', 'userCountry'));

            return $pdf->download('bookings-report.pdf');
        }
        if ($operation == 'download-csv') {
            if (class_exists('\Debugbar')) {
                \Debugbar::disable();
            }
            $csvExporter = new Export;

            $csvData = $bookings->map(function ($booking) use ($userCountry) {
                $converteAmount = convertCurrency($booking->total_amount, $userCountry['currency_code'], $booking->currency);
                $rooms = $booking->items
                    ->groupBy(fn ($item) => $item->room->details->title)
                    ->map(fn ($items, $title) => $title.' x '.$items->count())
                    ->implode(', ');

                return [
                    'booking_id' => $booking->id,
                    'reference_number' => $booking->reference_number,
                    'guest_name' => $booking->guest_name,
                    'guest_email' => $booking->guest_email,
                    'hotel_name' => $booking->hotel->name,
                    'rooms' => $rooms,
                    'stay' => Carbon::parse($booking->items->first()?->check_in)->format('d M').' - '.Carbon::parse($booking->items->first()?->check_out)->format('d M, Y'),
                    'status' => $booking->payment->status == 1 ? 'PAID' : 'PENDING',
                    'paid' => number_format($booking->total_amount, 2).' '.$booking->currency,
                    'paid_converted' => number_format(convertCurrency($booking->total_amount, $userCountry['currency_code'], $booking->currency), 2).' '.$userCountry['currency_code'],
                ];
            });

            return $csvExporter->build($csvData, [
                'booking_id' => 'ID',
                'reference_number' => 'Reference Number',
                'guest_name' => 'Guest Name',
                'guest_email' => 'Guest Email',
                'hotel_name' => 'Hotel Name',
                'rooms' => 'Rooms',
                'stay' => 'Stay Duration',
                'status' => 'Booking Status',
                'paid' => 'Paid Amount',
                'paid_converted' => 'Paid Converted Amount',
            ])->download('bookings_report_of_'.now()->format('d-M-y').'.csv');
        }
        if ($operation == 'print') {
            return view('admin.booking.print', compact('bookings', 'reportTitle', 'userCountry'));
        }
    }
}
