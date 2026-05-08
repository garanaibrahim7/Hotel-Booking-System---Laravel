<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Discount;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Room;
use App\Models\User;
use App\Notifications\BookingCancelNotification;
use App\Notifications\BookingConfirmNotification;
use App\Services\LocationService;
use App\Services\Payments\StripeProvider;
use App\Services\PaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laracsv\Export;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookingsOf = request('bookingsOf');
        $search = request('search');
        $date = request('date');
        $hotel = request('hotel');
        $roomDetailId = request('room_detail_id');

        $bookingsOf = request('bookingsOf');
        $filter = request('filter', 'today');
        $search = request('search');
        $date = request('date');
        // session()->put('previous_of_bookings', url()->previous());

        $query = BookingItem::with([
            'booking.user',
            'booking.payment',
            'room.details',
            'booking.hotel',
        ])
            ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->select('booking_items.*')
            ->orderBy('bookings.created_at', 'desc');

        if ($roomDetailId) {
            $query->whereHas('room', function ($q) use ($roomDetailId) {
                $q->where('room_detail_id', $roomDetailId);
            });
        } else {
            if ($hotel) {
                $query->whereHas('booking', function ($q) use ($hotel) {
                    $q->where('hotel_id', $hotel);
                });
            }

            if ($date) {
                $query->whereDate('bookings.created_at', $date);
            }

            if (! empty($bookingsOf) && $bookingsOf !== 'all') {
                // Log::channel('debug')->alert('Executed: '.$bookingsOf);
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
                        ->orWhereHas('booking.hotel', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })
                        ->orWhere('bookings.reference_number', 'like', "%{$search}%");
                });
            }
        }
        // dd($query);

        $totalBookings = (clone $query)->count();

        $upcomingStays = (clone $query)
            ->where('check_in', '>', now())
            ->count();

        $totalRevenue = (clone $query)
            ->join('payments', 'payments.booking_id', '=', 'booking_items.booking_id')
            ->where('payments.status', 1)
        // ->distinct('payments.booking_id')
            ->sum('payments.amount');

        $bookings = $query->paginate(10)->withQueryString();

        return view('admin.booking.list',
            compact('bookings',
                'search',
                'date',
                'totalBookings',
                'totalRevenue',
                'upcomingStays'
            ));
    }

    public function printList()
    {
        $userCountry = LocationService::fetchLocation();

        $bookingsOf = request('bookingsOf');
        $search = request('search');
        $date = request('date');
        $hotel = request('hotel');
        $operation = request('operation', 'print');

        // session()->put('previous_of_bookings', url()->previous());

        $query = Booking::with([
            'payment',
            'items.room.details',
            'hotel',
        ])
            // ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            // ->join('users', 'bookings.user_id', '=', 'users.id')
            // ->select('booking_items.*')
            ->orderBy('bookings.created_at', 'desc');

        if ($hotel) {
            $query->where('hotel_id', $hotel);
        }

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
        // return $bookingsOf;

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
        // dd($query);

        $bookings = $query->get();
        // return $bookings;

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

    // public function printListORG()
    // {
    //     $userCountry = LocationService::fetchLocation();

    //     $bookingsOf = request('bookingsOf');
    //     $search = request('search');
    //     $date = request('date');
    //     $hotel = request('hotel');
    //     $operation = request('operation', 'print');

    //     // session()->put('previous_of_bookings', url()->previous());

    //     $query = BookingItem::with([
    //         'booking.user',
    //         'booking.payment',
    //         'room.details',
    //         'booking.hotel',
    //     ])
    //         ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
    //         ->join('users', 'bookings.user_id', '=', 'users.id')
    //         ->select('booking_items.*')
    //         ->orderBy('bookings.created_at', 'desc');

    //     if ($hotel) {
    //         $query->whereHas('booking', function ($q) use ($hotel) {
    //             $q->where('hotel_id', $hotel);
    //         });
    //     }

    //     if ($date) {
    //         $query->whereDate('bookings.created_at', $date);
    //     }

    //     if (! empty($bookingsOf) && $bookingsOf !== 'all') {
    //         Log::channel('debug')->alert('Executed: '.$bookingsOf);
    //         $query->whereBetween('bookings.created_at', match ($bookingsOf) {
    //             'today' => [now()->startOfDay(), now()->endOfDay()],
    //             'week' => [now()->addDays(-7), now()->endOfDay()],
    //             'month' => [now()->startOfMonth(), now()->endOfMonth()],
    //             'year' => [now()->startOfYear(), now()->endOfYear()],
    //             default => [now()->startOfDay(), now()->endOfDay()],
    //         });
    //     } elseif (empty($bookingsOf) && ! $hotel) {
    //         $query->whereBetween('bookings.created_at', [now()->startOfDay(), now()->endOfDay()]);
    //     }
    //     // return $bookingsOf;

    //     if ($search) {
    //         $query->where(function ($q) use ($search) {
    //             $q->where('users.name', 'like', "%{$search}%")
    //                 ->orWhere('users.email', 'like', "%{$search}%")
    //                 ->orWhereHas('booking.hotel', function ($q) use ($search) {
    //                     $q->where('name', 'like', "%{$search}%");
    //                 })
    //                 ->orWhere('bookings.reference_number', 'like', "%{$search}%");
    //         });
    //     }
    //     // dd($query);

    //     $bookings = $query->get();

    //     $reportTitle = 'Booking Report - '.($date ?? ucfirst($bookingsOf ?? 'Today'));

    //     if ($operation == 'download-pdf') {
    //         $pdf = Pdf::loadView('admin.booking.print', compact('bookings', 'reportTitle', 'userCountry'));

    //         return $pdf->download('bookings-report.pdf');
    //     }
    //     if ($operation == 'download-csv') {
    //         if (class_exists('\Debugbar')) {
    //             \Debugbar::disable();
    //         }
    //         $csvExporter = new Export;

    //         $csvData = $bookings->map(function ($booking) use ($userCountry) {
    //             $converteAmount = convertCurrency($booking->total_amount, $userCountry['currency_code'], $booking->currency);

    //             return [

    //             ];
    //         });

    //         // $item->booking->reference_number
    //         // $item->booking->user->name
    //         // $item->booking->user->email
    //         // $item->booking->hotel->name
    //         // $item->room->details->title
    //         // \Carbon\Carbon::parse($item->check_in)->format('d M')
    //         // \Carbon\Carbon::parse($item->check_out)->format('d M, Y')
    //         // $item->booking->payment->status == 1 ? 'PAID' : 'PENDING'
    //         // number_format($item->booking->total_amount, 2) . ' ' . $item->booking->currency
    //         // number_format(convertCurrency($item->booking->total_amount, $userCountry['currency_code'], $item->booking->currency), 2) . ' ' . $userCountry['currency_code']

    //         return $csvExporter->build($bookings, [
    //             'booking.reference_number' => 'Ref #',
    //             'booking.user.name' => 'Guest Name',
    //             'booking.hotel.name' => 'Hotel',
    //             'check_in' => 'Check-In',
    //             'check_out' => 'Check-Out',
    //             'price' => 'Amount',
    //             'booking.payment.status' => 'Status',
    //         ])->download('bookings_report.csv');
    //     }
    //     if ($operation == 'print') {
    //         return view('admin.booking.print', compact('bookings', 'reportTitle', 'userCountry'));
    //     }
    // }

    public function report()
    {

        $totalBookings = Booking::count();

        $totalRevenue = Payment::where('status', 1)->sum('amount');

        $upcomingStays = DB::table('booking_items')
            ->where('check_in', '>', now())
            ->count();

        $bookingsOf = request('bookingsOf');
        $filter = request('filter', 'today');
        $search = request('search');
        $date = request('date');

        $query = BookingItem::with([
            'booking.user',
            'booking.payment',
            'room.details',
            'booking.hotel',
        ])
            ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->select('booking_items.*')
            ->orderBy('bookings.created_at', 'desc');

        if ($date) {
            $query->whereDate('bookings.created_at', $date);
        }

        if ($bookingsOf !== 'all') {

            $query->whereBetween('bookings.created_at', match ($bookingsOf) {
                'today' => [now()->startOfDay(), now()->endOfDay()],
                'week' => [now()->addDays(-7), now()->endOfDay()],
                'month' => [now()->startOfMonth(), now()->endOfMonth()],
                'year' => [now()->startOfYear(), now()->endOfYear()],
                default => [now()->startOfDay(), now()->endOfDay()],
            });
        } elseif (empty($bookingsOf)) {
            $query->whereBetween('bookings.created_at', [now()->startOfDay(), now()->endOfDay()]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhereHas('booking.hotel', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('bookings.reference_number', 'like', "%{$search}%");
            });
        }

        $bookings = $query->get();

        return view('admin.booking.reports', compact('bookings', 'totalBookings', 'totalRevenue', 'upcomingStays'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        return view('admin.booking.detail', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //
    }

    public function liveBookings()
    {
        $today = Carbon::today();
        $user = Auth::user();
        $isManager = $user->role === 'manager';

        $arrivalsQuery = BookingItem::with(['booking.user', 'room', 'booking.hotel'])
            ->whereDate('check_in', $today)
            ->whereHas('booking', function ($query) {
                $query->where('status', 1);
            });

        $checkoutsQuery = BookingItem::with(['booking.user', 'room', 'room.details.hotel'])
            ->whereDate('check_out', $today)
            ->whereHas('booking', function ($q) {
                $q->whereNotNull('arrival');
            })
            ->whereHas('booking', function ($query) {
                $query->where('status', 1);
            });

        $ongoingStaysQuery = BookingItem::with(['booking.user', 'room', 'room.details.hotel'])
            ->whereDate('check_in', '<', $today)
            ->whereHas('booking', function ($q) {
                $q->whereNotNull('arrival');
            })
            ->whereDate('check_out', '>=', $today)
            ->whereHas('booking', function ($query) {
                $query->where('status', 1);
            });

        if ($isManager) {
            $arrivals = $arrivalsQuery->whereHas('booking.hotel', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->orderBy('check_in', 'asc')
                ->get();

            $checkouts = $checkoutsQuery->whereHas('booking.hotel', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->orderBy('check_out', 'asc')
                ->get();
            $ongoingStays = $ongoingStaysQuery->whereHas('booking.hotel', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->orderBy('check_out', 'asc')
                ->get();
        } else {
            $arrivals = $arrivalsQuery->orderBy('check_in', 'asc')
                ->get();
            $checkouts = $checkoutsQuery->orderBy('check_out', 'asc')
                ->get();
            $ongoingStays = $ongoingStaysQuery->orderBy('check_out', 'asc')
                ->get();
        }

        return view('admin.booking.live-bookings', compact('arrivals', 'checkouts', 'ongoingStays', 'today'));
    }

    public function markSuccess(Booking $booking)
    {
        try {
            $booking->update([
                'status' => Booking::STATUS_CONFIRMED,
            ]);
            $booking->payment()->update([
                'status' => Payment::STATUS_SUCCESS,
                'gateway' => 'offline',
            ]);
            if ($booking->discount_id) {
                Discount::findOrFail($booking->discount_id)
                    ->increment('used_count');
            }
            Cache::forget('rooms_city_'.($booking->hotel->city_id ?? 'all'));

            session()->forget('checkoutPayload');
            session()->forget('changedStay');
            session()->forget('stay');
            session()->forget('booking_hotel_id');

            $user = $booking->user;
            $user->notify(new BookingConfirmNotification($booking));

            return back()->with('success', 'Booking Marked Confirmed');

        } catch (Exception $e) {
            Log::channel('failures')->critical('Booking Mark Confirm Failed. '.$e->getMessage());

            return back()->with('error', 'Booking Marked Confirm Failed');
        }

    }

    public function markArrival(Booking $booking)
    {
        $booking->update([
            'arrival' => date(now()),
        ]);

        return back()->with('success', 'Guest Marked Arrived');
    }

    public function markLeaved(Booking $booking)
    {
        $booking->update([
            'leaved' => date(now()),
        ]);

        return back()->with('success', 'Guest Marked Leaved');
    }

    public function cancelBooking(Booking $booking)
    {
        $booking->update([
            'status' => Booking::STATUS_REJECTED,
        ]);
        $booking->payment()->update([
            'status' => Payment::STATUS_CANCELLED,
        ]);

        $user = $booking->user;
        $user->notify(new BookingCancelNotification($booking));

        if ($booking->discount_id) {
            Discount::findOrFail($booking->discount_id)
                ->decrement('used_count');
        }

        return back()->with('success', 'Booking Has been Cancelled');
    }

    public function processRefund(Refund $refund)
    {
        $paymentObj = new PaymentService(new StripeProvider);
        $response = $paymentObj->refund($refund->payment_id, $refund->amount - 20, $refund->reason);
        Log::channel('debug')->error('Refund ID : ', ['response' => $response, 'refund' => $refund]);
        $refund_id = $response['data']['refund_id'] ?? null;

        $refund->update([
            'status' => Refund::STATUS_PROCESSING,
            'refund_id' => $refund_id,
        ]);

        return back()->with($response);
    }

    public function directBook(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'required|string',
            'payment_method' => 'required|in:cash,card,upi,stripe',
        ]);

        // 1. Create or Find User (Guest)
        $user = User::firstOrCreate(
            ['phone' => $request->phone],
            ['name' => $request->name, 'email' => $request->email, 'password' => Hash::make(Str::random(10))]
        );

        // 2. Create the Main Booking
        $booking = Booking::create([
            'user_id' => $user->id,
            'booking_number' => 'ADM-'.strtoupper(Str::random(8)),
            'total_price' => $request->total_price, // Calculate based on nights * room price
            'status' => 1, // Auto-confirmed for Admin
            'special_requests' => $request->instructions,
            'arrival_at' => $request->check_in == now()->toDateString() ? now() : null,
        ]);

        // 3. Create Booking Item (The Room Allocation)
        $booking->items()->create([
            'room_id' => $request->room_id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'price' => $request->total_price,
        ]);

        // 4. Record Payment
        $booking->payment()->create([
            'method' => $request->payment_method,
            'amount' => $request->total_price,
            'status' => 'paid',
        ]);

        return back()->with(['message' => 'Room booked successfully!', 'type' => 'success']);
    }
}
