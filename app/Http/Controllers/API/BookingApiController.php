<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\CheckoutService;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingApiController extends Controller
{
    use HttpResponses;

    public function index(Request $request)
    {
        $bookings = Booking::with(['items.room.details', 'payment'])
            ->where('user_id', Auth::guard('sanctum')->id())
            ->latest()
            ->paginate(10);

        $transformedBookings = $bookings->getCollection()->map(function ($booking) {

            $roomSummary = $booking->items->groupBy(function ($item) {
                return $item->room->details?->title ?? 'Unknown Room';
            })->map(function ($group, $title) {
                return $title.' x '.$group->count();
            })->implode(', ');

            $firstItem = $booking->items->first();
            $checkIn = $firstItem ? Carbon::parse($firstItem->check_in) : null;
            $checkOut = $firstItem ? Carbon::parse($firstItem->check_out) : null;

            $statusLabel = 'Cancelled';
            if ($booking->status == 1) {
                $statusLabel = $checkOut?->isPast() ? 'Stay Complete' : 'Confirmed';
            } elseif ($booking->status == 0) {
                $statusLabel = 'Pending';
            } elseif ($booking->status == 3) {
                $statusLabel = 'Processing';
            }

            return [
                'reference_number' => $booking->reference_number,
                'booking_date' => $booking->created_at->format('d M, Y'),
                'room_summary' => $roomSummary,
                'stay_dates' => [
                    'check_in' => $checkIn?->format('d M'),
                    'check_out' => $checkOut?->format('d M'),
                    'full_stay' => $checkIn?->format('d M').' - '.$checkOut?->format('d M'),
                ],
                'payment' => [
                    'amount' => number_format($booking->payment->converted_amount, 2),
                    'currency' => $booking->payment->paid_currency,
                    'display' => number_format($booking->payment->converted_amount, 2).' '.$booking->payment->paid_currency,
                ],
                'status' => [
                    'code' => $booking->status,
                    'label' => $statusLabel,
                ],
                'links' => [
                    'print' => $booking->status == 1 ? route('booking.print_invoice', $booking->reference_number) : null,
                ],
            ];
        });

        $message = $transformedBookings->isEmpty() ? "You didn't made any Bookings yet" : 'Bookings retrived Successfully';

        return $this->success([
            'bookings' => $transformedBookings,
            'meta' => [
                'total' => $bookings->total(),
                'current_page' => $bookings->currentPage(),
                'per_page' => $bookings->perPage(),
                'last_page' => $bookings->lastPage(),
            ],
        ], $message);
    }

    public function store(Request $request)
    {
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

        if (! $paymentData) {
            return $this->error(null, 'Server Process Error', 422);
        }

        // Log::channel('debug')->info('Payment Data: ', $paymentData);

        if (isset($paymentData['error'])) {
            return $this->error(null, $paymentData['error'], 422);
        }

        return $this->success([
            'payment_url' => $paymentData['url'],
        ], 'Booking initiated successfully.');
    }

    public function cancelBooking(Request $request)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $booking = Booking::with('hotel')->findOrFail($request->booking_id);
        $user = auth('sanctum')->user();
        if ($booking->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($booking->status !== Booking::STATUS_CONFIRMED) {
            return redirect()->back()->with('error', 'This booking cannot be cancelled.');
        }

        $totalPaid = $booking->total_amount;
        $cancellationFee = $booking->hotel->cancellation_charge ?? 0;

        if ($cancellationFee > $totalPaid) {
            $cancellationFee = $totalPaid;
        }

        $refundAmount = $totalPaid - $cancellationFee;

        return $this->success(null, 'Reservation cancelled successfully. Refund of '.$booking->currency_symbol.$refundAmount.' '.$booking->currency.' initiated.');

        try {
            DB::beginTransaction();

            $booking->update([
                'status' => Booking::STATUS_CANCELLED,
                'cancellation_reason' => $request->cancellation_reason,
                'cancelled_at' => now(),
                'refund_amount' => $refundAmount,
            ]);
            DB::commit();

            return $this->success(null, 'Reservation cancelled successfully. Refund of '.$booking->currency_symbol.$refundAmount.' initiated.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cancellation Error: '.$e->getMessage());

            return $this->error(null, 'Something went wrong during cancellation. Please try again.', 422);
        }
    }

    public function show($id)
    {
        $booking = Booking::find($id);
        if (! $booking) {
            return response('Booking Not Found', 404);
        }

        $payment = $booking->payment;
        $firstItem = $booking->items->first();

        $checkOut = Carbon::parse($firstItem->check_out);
        $isPast = $checkOut->isPast();

        $nights = Carbon::parse($firstItem->check_in)->diffInDays($checkOut) ?: 1;

        $calculatedSubtotal = 0;
        $items = $booking->items->map(function ($item) use ($payment, $nights, &$calculatedSubtotal) {
            $itemTotal = $item->price_at_booking * $payment->exchange_rate * $nights;
            $calculatedSubtotal += $itemTotal;

            return [
                'room_number' => $item->room->room_number,
                'category' => $item->room->details->category,
                'type' => $item->room->details->type,
                'check_in' => Carbon::parse($item->check_in)->format('d M, Y'),
                'check_out' => Carbon::parse($item->check_out)->format('d M, Y'),
                'nights' => $nights,
                'price_per_night' => round($item->price_at_booking * $payment->exchange_rate, 2),
                'total' => round($itemTotal, 2),
            ];
        });

        $savings = $calculatedSubtotal - $payment->converted_amount;

        return [
            'reference' => $booking->reference_number,
            'status' => [
                'code' => $booking->status,
                'label' => $this->getBookingStatusLabel($booking->status, $isPast),
                'is_completed' => $isPast && $payment->status == 1,
            ],
            'hotel' => [
                'name' => $booking->hotel->name,
                'address' => $booking->hotel->address,
                'pincode' => $booking->hotel->pincode,
            ],
            'guest' => [
                'name' => $booking->user->name,
                'email' => $booking->user->email,
                'mobile' => $booking->user->mobile,
            ],
            'items' => $items,
            'pricing' => [
                'subtotal' => round($calculatedSubtotal, 2),
                'discount_applied' => $savings > 0.5 ? round($savings, 2) : 0,
                'total_paid' => round($payment->converted_amount, 2),
                'currency' => strtoupper($payment->paid_currency),
            ],
            'review' => $booking->review ? [
                'rating' => $booking->review->rating,
                'comment' => $booking->review->comment,
                'scores' => [
                    'food' => $booking->review->food,
                    'services' => $booking->review->services,
                    'hospitality' => $booking->review->hospitality,
                    'cleaning' => $booking->review->cleaning,
                ],
                'created_at' => $booking->review->created_at->format('d M, Y'),
            ] : null,
            'actions' => [
                'can_cancel' => ! $isPast && $booking->status == 1,
                'can_refund' => ! $isPast && $booking->status == 5,
                'can_invoice' => $isPast && $payment->status == 1,
                'can_review' => $isPast && $payment->status == 1 && ! $booking->review,
            ],
        ];
    }

    private function getBookingStatusLabel($status, $completed)
    {
        return match ($status) {
            0 => 'Pending',
            1 => $completed ? 'Stay Complete' : 'Confirmed',
            2 => 'Failed/Cancelled',
            3 => 'Processing',
            5 => 'Rejected',
            default => 'Cancelled',
        };
    }
}
