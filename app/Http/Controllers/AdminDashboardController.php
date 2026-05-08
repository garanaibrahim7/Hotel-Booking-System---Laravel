<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;
use App\Services\LocationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $adminCountry = LocationService::fetchLocation();
        $today = Carbon::today();

        $totalUsers = User::count();
        $totalHotels = Hotel::count();
        $totalRooms = Room::where('status', 1)->count();
        // return $totalRooms;
        $todaysBookings = Booking::whereDate('created_at', $today)->count();

        $totalBookings = Booking::count();

        $totalRevenue = Booking::where('status', Booking::STATUS_CONFIRMED)->get(['total_amount', 'currency'])->reduce(function ($carry, $booking) use ($adminCountry) {
            return $carry + (convertCurrency($booking->total_amount, $adminCountry['currency_code'], $booking->currency));
        });

        $upcomingStays = DB::table('booking_items')
            ->where('check_in', '>', now())
            ->count();

        $topCities = DB::table('cities')
            ->join('hotels', 'cities.id', '=', 'hotels.city_id')
            ->join('bookings', 'hotels.id', '=', 'bookings.hotel_id')
            ->select('cities.name', DB::raw('count(bookings.id) as bookings_count'))
            ->groupBy('cities.id', 'cities.name')
            ->orderByDesc('bookings_count')
            // ->limit(5)
            ->get();

        $latestReviews = Review::with('user')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $occupancyData = [];
        $occupancyLabels = [];
        // $totalRooms = DB::table('rooms')->count();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $occupancyLabels[] = $date->format('D');
            $occupiedCount = DB::table('booking_items')
                ->whereDate('check_in', '<=', $date)
                ->whereDate('check_out', '>', $date)
                ->count();

            $percentage = $totalRooms > 0 ? ($occupiedCount / $totalRooms) * 100 : 0;

            $occupancyData[] = round(min($percentage, 100), 1);
        }

        // return compact(
        //     'totalUsers',
        //     'totalHotels',
        //     'totalRooms',
        //     'todaysBookings',
        //     'totalBookings',
        //     'totalRevenue',
        //     'upcomingStays',
        //     'topCities',
        //     'latestReviews',
        //     'currentYearData',
        //     'lastYearData',
        //     'occupancyData',
        //     'occupancyLabels',
        // );

        return view('admin.dashboard', compact(
            'adminCountry',
            'totalUsers',
            'totalHotels',
            'totalRooms',
            'todaysBookings',
            'totalBookings',
            'totalRevenue',
            'upcomingStays',
            'topCities',
            'latestReviews',
            'occupancyData',
            'occupancyLabels'
        ));

        return view('admin.dashboard');
    }

    public function bookingChartData(Request $request)
    {
        // sleep(1);
        $filter = $request->input('filter', 'daily');
        $currentYear = date('Y');
        $lastYear = $currentYear - 1;
        $currentData = [];
        $lastData = [];
        $categories = [];

        switch ($filter) {
            case 'daily':
                // $daysInMonth = date('t');
                $daysInMonth = 30;
                $categories = range(1, $daysInMonth);

                $currentData = $this->getBookingCounts('DAY(created_at)', 'DAY(created_at)', [
                    ['year', $currentYear],
                    ['month', date('m')],
                ], $daysInMonth);

                $lastData = $this->getBookingCounts('DAY(created_at)', 'DAY(created_at)', [
                    ['year', $lastYear],
                    ['month', date('m')],
                ], $daysInMonth);

                $subtitle = 'Daily performance for '.date('F');
                break;

            case 'weekly':
                $currentWeek = (int) date('W');

                $categories = [];
                $weekNumbers = [];
                for ($i = -2; $i <= 2; $i++) {
                    $w = $currentWeek + $i;
                    $weekNumbers[] = $w;
                    $categories[] = ($i === 0) ? "Week $w (Now)" : "Week $w";
                }

                $currentData = $this->getRollingWeeklyCounts($weekNumbers, $currentYear);
                $lastData = $this->getRollingWeeklyCounts($weekNumbers, $lastYear);

                $subtitle = 'Weekly Trend: Past 2 Weeks & Next 2 Weeks';
                break;

            case 'monthly':
            default:
                $categories = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                $currentData = $this->getBookingCounts('MONTH(created_at)', 'MONTH(created_at)', [
                    ['year', $currentYear],
                ], 12);

                $lastData = $this->getBookingCounts('MONTH(created_at)', 'MONTH(created_at)', [
                    ['year', $lastYear],
                ], 12);

                $subtitle = 'Monthly performance vs Last Year';
                break;
        }

        return response()->json([
            'categories' => $categories,
            'current' => $currentData,
            'last' => $lastData,
            'subtitle' => $subtitle,
        ]);
    }

    public function financialChartData(Request $request)
    {
        $filter = $request->input('filter', 'daily');
        $location = LocationService::fetchLocation();
        $toCurrency = $location['currency_code'] ?? 'USD';

        $revenueData = [];
        $refundData = [];
        $categories = [];

        switch ($filter) {
            case 'daily':
                for ($i = 29; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $categories[] = $date->format('d M');

                    // Fetch for specific date
                    $revenueData[] = $this->getSumAndConvert('credit', $toCurrency, $date->startOfDay(), $date->copy()->endOfDay());
                    $refundData[] = $this->getSumAndConvert('debit', $toCurrency, $date->startOfDay(), $date->copy()->endOfDay());
                }
                $subtitle = "Last 30 Days ($toCurrency)";
                break;

            case 'weekly':
                // Logic: Last 12 weeks including the current partial week
                for ($i = 11; $i >= 0; $i--) {
                    $start = now()->subWeeks($i)->startOfWeek();
                    $end = now()->subWeeks($i)->endOfWeek();
                    $categories[] = 'W'.$start->format('W');

                    $revenueData[] = $this->getSumAndConvert('credit', $toCurrency, $start, $end);
                    $refundData[] = $this->getSumAndConvert('debit', $toCurrency, $start, $end);
                }
                $subtitle = "Last 12 Weeks ($toCurrency)";
                break;

            case 'monthly':
            default:
                // Logic: Last 12 months including the current month
                for ($i = 11; $i >= 0; $i--) {
                    $month = now()->subMonths($i);
                    $categories[] = $month->format('M y');

                    $revenueData[] = $this->getSumAndConvert('credit', $toCurrency, $month->copy()->startOfMonth(), $month->copy()->endOfMonth());
                    $refundData[] = $this->getSumAndConvert('debit', $toCurrency, $month->copy()->startOfMonth(), $month->copy()->endOfMonth());
                }
                $subtitle = "Last 12 Months ($toCurrency)";
                break;
        }

        return response()->json([
            'categories' => $categories,
            'revenue' => $revenueData,
            'refunds' => $refundData,
            'subtitle' => $subtitle,
            'currency' => $toCurrency,
        ]);
    }

    /**
     * Sums transactions and converts to user currency
     */
    private function getSumAndConvert($type, $toCurrency, $start, $end)
    {
        // Sum only 'converted_amount' because it's your standardized base currency
        $baseAmount = (float) DB::table('transactions')
            ->where('type', $type)
            ->whereBetween('created_at', [$start, $end])
            ->sum('converted_amount');

        if ($baseAmount <= 0) {
            return 0;
        }

        // Assuming your converted_amount in DB is stored as USD.
        // If it's SAR, change 'USD' to 'SAR'.
        return round(convertCurrency($baseAmount, $toCurrency, 'USD'), 2);
    }

    /**
     * Helper to fetch and fill missing data points
     */
    private function getBookingCounts($selectRaw, $groupByRaw, $whereConditions, $maxRange)
    {
        $query = DB::table('bookings')->select(DB::raw("$selectRaw as label"), DB::raw('count(*) as count'));

        foreach ($whereConditions as $condition) {
            if ($condition[0] == 'year') {
                $query->whereYear('created_at', $condition[1]);
            }
            if ($condition[0] == 'month') {
                $query->whereMonth('created_at', $condition[1]);
            }
        }

        $raw = $query->groupBy(DB::raw($groupByRaw))->pluck('count', 'label')->all();

        $data = [];
        for ($i = 1; $i <= $maxRange; $i++) {
            $data[] = $raw[$i] ?? 0;
        }

        return $data;
    }

    private function getRollingWeeklyCounts(array $weekNumbers, $year)
    {
        $raw = DB::table('bookings')
            ->select(DB::raw('WEEK(created_at, 1) as week_no'), DB::raw('count(*) as count'))
            ->whereYear('created_at', $year)
            ->whereIn(DB::raw('WEEK(created_at, 1)'), $weekNumbers)
            ->groupBy('week_no')
            ->pluck('count', 'week_no')
            ->all();

        $data = [];
        foreach ($weekNumbers as $week) {
            $data[] = $raw[$week] ?? 0;
        }

        return $data;
    }
}
