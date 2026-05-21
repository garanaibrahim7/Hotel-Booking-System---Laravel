<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Discount;
use App\Services\CheckoutService;
use App\Services\DiscountCoupenService;
use App\Services\LocationService;
use App\Services\RoomsFindService;
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

    public function checkout()
    {
        $stay = session()->get('stay', []);

        if (empty($stay) || empty($stay['items'])) {
            return response()->json([
                'success' => false,
                'message' => 'Your room selection is completely empty!',
            ], 400);
        }

        $checkIn = session('booking_check_in');
        $checkOut = session('booking_check_out');

        if (! $checkIn || ! $checkOut) {
            return response()->json([
                'success' => false,
                'message' => 'Required stay duration parameters are missing from session cache.',
            ], 422);
        }

        $roomRequirements = array_map(fn ($item) => [
            'id' => $item['id'],
            'quantity' => $item['quantity'],
        ], $stay['items']);

        $availabilityData = RoomsFindService::loadRequiredRooms($roomRequirements, $checkIn, $checkOut);

        if ($availabilityData->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Selected rooms are no longer available for your chosen dates.',
            ], 410);
        }

        $availableRooms = $availabilityData['rooms'];
        $hotel = $availabilityData['hotel'];
        $nights = Carbon::parse($checkIn)->diffInDays($checkOut) ?: 1;

        $userCountry = LocationService::fetchLocation();
        $hotelCountry = $hotel->city->state->country;

        $exchangeRate = 1;
        if ($userCountry['currency_code'] != $hotelCountry->currency_code) {
            $exchangeRate = convertCurrency(1, $userCountry['currency_code'], $hotelCountry->currency_code);
        }

        $rawTotal = $availableRooms->sum('details.price') * $nights;
        $coupon_code = null;

        if (isset($stay['discount_id'])) {
            $discount = Discount::find($stay['discount_id']);
            if ($discount && $discount->active_status) {
                $coupon_code = $discount->coupen_code;
            }
        }

        if ($coupon_code) {
            $validatedDiscount = DiscountCoupenService::validateCoupen(
                $rawTotal,
                $coupon_code,
                $nights,
                $hotel->id,
                $userCountry['country_id'],
                $exchangeRate
            );

            if (isset($validatedDiscount['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => $validatedDiscount['error'],
                ], 422);
            }
        } else {
            $validatedDiscount = [
                'coupon_id' => null,
                'coupon_code' => null,
                'discount_amount' => 0,
                'final_amount' => round($rawTotal, 2),
                'final_converted_amount' => round($rawTotal * $exchangeRate, 2),
            ];
        }

        $checkoutPayload = [
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'nights' => $nights,
            'currency_symbol' => $userCountry['currency_symbol'],
            'hotelCurrencySymbol' => $hotelCountry->currency_symbol,
            'country_id' => $hotelCountry->id,
            'userCountryId' => $userCountry['country_id'],
            'converted' => $userCountry['currency_code'] != $hotelCountry->currency_code,
            'user' => Auth::guard('sanctum')->user() ? [
                'name' => Auth::guard('sanctum')->user()->name,
                'email' => Auth::guard('sanctum')->user()->email,
                'phone' => Auth::guard('sanctum')->user()->phone,
            ] : null,

            'hotel' => [
                'id' => $hotel->id,
                'name' => $hotel->name,
                'city' => $hotel->city->name,
                'state' => $hotel->city->state->name,
                'address' => $hotel->address,
                'pincode' => $hotel->pincode,
            ],

            'rooms' => $availableRooms->map(function ($room) use ($exchangeRate) {
                return [
                    'id' => $room->details->id,
                    'title' => $room->details->title,
                    'path' => $room->details->images->first()->path ?? 'default.jpg',
                    'price' => round($room->details->price * $exchangeRate, 2),
                ];
            })->values()->all(),

            'finalActualTotal' => $rawTotal,
            'subTotal' => round($rawTotal * $exchangeRate, 2),
            'finalTotal' => $validatedDiscount['final_converted_amount'],
            'discountId' => $validatedDiscount['coupon_id'] ?? null,
            'discountCode' => $validatedDiscount['coupon_code'] ?? null,
            'discountAmount' => round($validatedDiscount['discount_amount'] * $exchangeRate, 2) ?? null,
        ];

        session()->put('checkoutPayload', $checkoutPayload);
        // sleep(2);

        return response()->json([
            'success' => true,
            'coupon_code' => $coupon_code,
            'data' => $checkoutPayload,
        ], 200);

    }

    public function applyCoupon(Request $request)
    {
        // sleep(2);
        $couponCode = $request->couponCode;
        $totalAmount = $request->totalAmount;
        $nights = $request->nights;
        $hotelId = $request->hotelId;

        $userCountry = LocationService::fetchLocation();
        $userCountryId = $userCountry['country_id'];

        $validatedCouponDetails = DiscountCoupenService::validateCoupen($totalAmount, $couponCode, $nights, $hotelId, $userCountryId);

        if (isset($validatedCouponDetails['error'])) {
            return response()->json($validatedCouponDetails);
        }
        if ($validatedCouponDetails['status']) {
            $checkoutPayload = session()->get('checkoutPayload');

            $stay = session()->get('stay');

            if ($stay) {
                $stay['discount_id'] = $validatedCouponDetails['coupon_id'];
                $stay['offer_message'] = 'Coupon Applied : '.$validatedCouponDetails['coupon_code'];

                session()->put('stay', $stay);
                session()->put('changedStay', true);
            }

            return response()->json([
                'status' => 'success',
                'data' => $validatedCouponDetails,
            ]
            );
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Some Services are Not Working at Moment']
        );
    }

    public function removeCoupon()
    {
        $stay = session()->get('stay', []);
        if ($stay) {
            unset($stay['discount_id']);
            unset($stay['offer_message']);

            session()->put('stay', $stay);
            session()->put('changedStay', true);
        }


        $checkoutPayload = session()->get('checkoutPayload');
        if ($checkoutPayload) {
            unset($checkoutPayload['discountId']);
            unset($checkoutPayload['discountCode']);
            unset($checkoutPayload['discountAmount']);
            $checkoutPayload['finalTotal'] = $checkoutPayload['subTotal'];

            session()->put('checkoutPayload', $checkoutPayload);
        }

        return response()->json([
            'status' => true,
            'message' => 'Coupon Removed Successfully',
            'data' => [
                'finalTotal' => $checkoutPayload['subTotal'] ?? null,
            ],
        ]);
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

        return response()->json([
            'status' => 'success',
            'message' => 'Payment Initialized Successfully',
            'data' => [
                'payment_url' => $paymentData['url'],
                'session_id' => $paymentData['session_id'],
            ],
        ]);
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
