<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Country;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\RoomDetail;
use App\Services\CheckoutService;
use App\Services\Payments\StripeProvider;
use App\Services\PaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserBookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['items.room.details', 'payment'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);
        // return $bookings;

        return view('client.bookings', compact('bookings'));
    }

    public function create(Request $request)
    {

        $room_detail_id = $request->input('room_detail_id');
        $roomDetail = RoomDetail::findOrFail($room_detail_id);

        $userCountry = Country::where('iso_code', env('TEMP_LOCATION'))->first()
            ?? Country::where('iso_code', 'US')->first();

        $selectedRooms = collect([$roomDetail])->map(function ($room) use ($userCountry) {
            return (object) [
                ...$room->ToArray(),
                'title' => $room->title,
                'converted_price' => convertCurrency($room->price, $userCountry->currency_code, $room->hotel->city->state->country->currency_code),
                'converted_currency_symbol' => $userCountry->currency_symbol,
                'currency_symbol' => $room->hotel->city->state->country->currency_symbol,
                'currency_code' => $room->hotel->city->state->country->currency_code,
            ];
        });
        $subtotal = $selectedRooms->sum('line_total');

        return $selectedRooms;

        return view('client.checkout', compact('selectedRooms', 'userCountry', 'subtotal'));
    }

    public function store(Request $request)
    {
        // return $request;
        $requestDetails = $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'coupon_code' => 'nullable|exists:discounts,coupen_code',
            'room_requirements' => 'required|array|min:1',
            'instructions' => 'nullable|string',
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|numeric',
        ], [
            'check_in.after_or_equal' => 'You cannot book a room for a past date.',
            'check_out.after' => 'The check-out date must be at least one day after your arrival.',
        ]);

        $paymentData = CheckoutService::processCheckout($requestDetails);
        // return $paymentData;
        if (! $paymentData) {
            return back()->with('error', 'An error occurred during booking. Please try again.');
        }
        if (isset($paymentData['error'])) {
            return back()->with('error', $paymentData['error']);
        }

        return redirect($paymentData['url']);
    }

    public function show($referenceNumber)
    {
        if ($referenceNumber === 'none') {
            return redirect()->route('client.home')->with('error', 'Bad Request');
        }
        $payment = Payment::with(['booking.items.room.details'])
            ->whereHas('booking', function ($query) use ($referenceNumber) {
                $query->where('reference_number', $referenceNumber);
            })
            ->firstOrFail();
        if ($payment->booking->user_id != Auth::id()) {
            return abort(403, 'Invalid Access');
        }
        if (! $payment || ! $payment->booking) {
            return abort(404, 'Booking record not found.');
        }

        return view('client.booking.view-booking', compact('payment'));
    }

    public function paymentSuccess(Request $request)
    {
        $sessionId = $request['session_id'] ?? null;

        if ($sessionId) {
            $payment = Payment::with(['booking'])
                ->where('session_id', $sessionId)
                ->firstOrFail();
            if ($payment->status === Payment::STATUS_SUCCESS) {
                session()->forget('stay');
            }
            if ($payment->booking->user_id != Auth::id()) {
                return abort(403, 'Invalid Access');
            }
            if (! $payment || ! $payment->booking) {
                return abort(404, 'Booking record not found.');
            }

            return view('client.payment.success', compact('payment'));
        } else {
            return redirect()->route('client.home')->with('error', 'Bad Request');
        }
    }

    public function paymentCancel(Request $request)
    {

        $session_id = $request['session_id'];

        $payment = Payment::where('session_id', $session_id)
            ->with(['booking.items.room.details'])
            ->firstOrFail();

        if ($payment->booking->user_id != Auth::id()) {
            return abort(403, 'Invalid Access');
        }

        if ($payment->session_id && $payment->status != Payment::STATUS_FAILED && $payment->status != Payment::STATUS_SUCCESS) {
            $service = new PaymentService(new StripeProvider);
            $service->cancelBooking($payment->session_id);
            $service->finalizePayment($payment->id, Booking::STATUS_FAILED, null);
        }

        // return $payment;
        return view('client.payment.success', compact('payment'));
    }

    public function printInvoice($reference_no)
    {
        $payment = Payment::with(['booking.items.room.details', 'booking.user.profile'])
            ->whereHas('booking', function ($q) use ($reference_no) {
                $q->where('reference_number', $reference_no);
            })->firstOrFail();

        if (Auth::user()->role != 'admin' && $payment->booking->user_id != Auth::id()) {
            return abort(403, 'Invalid Access');
        }

        if ($payment->status != Payment::STATUS_SUCCESS) {
            return abort(404);
        }

        return view('client.payment.print-invoice', compact('payment'));
    }

    public function downloadInvoice($reference_no)
    {
        $payment = Payment::with(['booking.items.room.details', 'booking.user.profile'])
            ->whereHas('booking', function ($q) use ($reference_no) {
                $q->where('reference_number', $reference_no);
            })->firstOrFail();
        if ($payment->booking->user_id != Auth::id()) {
            return abort(403, 'Invalid Access');
        }
        $pdf = Pdf::loadView('client.payment.invoice', ['payment' => $payment]);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('invoice_'.$reference_no.'.pdf');
    }

    public function cancelBooking($reference_number)
    {
        // $paymentObj = new PaymentService(new StripeProvider());
        // return $paymentObj->refund(3, 10);

        $booking = Booking::where('reference_number', $reference_number)
            ->first();

        return view('client.payment.cancel-booking', compact('booking'));
    }

    public function finalCancelBooking(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $booking = Booking::with('hotel')->findOrFail($id);
        // return $booking;

        if ($booking->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($booking->status !== Booking::STATUS_CONFIRMED) {
            return redirect()->back()->with('error', 'This booking cannot be cancelled.');
        }

        $totalPaid = $booking->payment->converted_amount;
        $cancellationFee = ($booking->hotel->cancellation_charge ?? 0) * $booking->payment->exchange_rate;

        if ($cancellationFee > $totalPaid) {
            $cancellationFee = $totalPaid;
        }

        $refundAmount = $totalPaid - $cancellationFee;

        try {
            Refund::create([
                'booking_id' => $booking->id,
                'payment_id' => $booking->payment->id,
                'user_id' => auth()->id(),
                'refund_id' => null,
                'amount' => $refundAmount,
                'currency' => $booking->payment->paid_currency,
                'status' => Refund::STATUS_PENDING,
                'reason' => $request->cancellation_reason ?? 'Booking Cancelled',
                'note' => null,
            ]);

            return redirect()->route('booking.all')
                ->with('success', 'Reservation cancelled successfully. Refund of '.$booking->currency_symbol.$refundAmount.' initiated.');
        } catch (\Exception $e) {
            Log::error('Cancellation Error: '.$e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong during cancellation. Please try again.');
        }
    }
}
