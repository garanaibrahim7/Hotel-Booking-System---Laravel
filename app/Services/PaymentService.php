<?php

namespace App\Services;

use App\Contracts\PaymentProviderInterface;
use App\Jobs\CreateTransactionJob;
use App\Models\Booking;
use App\Models\Discount;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\User;
use App\Notifications\AlertAdminPaymentFail;
use App\Notifications\BookingCancelNotification;
use App\Notifications\BookingConfirmNotification;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class PaymentService
{
    public function __construct(protected PaymentProviderInterface $paymentProvider) {}

    public function initializeBooking($payload)
    {
        $paymentData = $this->paymentProvider->createPaymentSession($payload);

        // Log::channel('debug')->info('Payment Data at Payment Service Class : ', $paymentData);
        return [
            'session_id' => $paymentData['session_id'],
            'url' => $paymentData['url'],
            'gateway' => $this->paymentProvider->getProviderName(),
            'currency' => strtoupper($payload['currency']),
        ];
    }

    public static function finalizePayment($bookingId, int $status, $paymentIntentId): bool
    {
        try {
            if (($bookingId === null || empty($bookingId)) && $paymentIntentId !== null) {
                $booking = Booking::with('user', 'payment')
                    ->whereHas('payment', function ($q) use ($paymentIntentId) {
                        $q->where('payment_intent_id', $paymentIntentId);
                    })->first();
            } else {
                $booking = Booking::with('user')->findOrFail($bookingId);
            }

            switch ($status) {
                case 1:
                    $booking->update([
                        'status' => Booking::STATUS_CONFIRMED,
                    ]);

                    Payment::where('booking_id', $booking->id)->update([
                        'payment_intent_id' => $paymentIntentId ?? null,
                        'status' => Payment::STATUS_SUCCESS,
                    ]);

                    if ($booking->discount_id) {
                        Discount::findOrFail($booking->discount_id)
                            ->increment('used_count');
                    }

                    CreateTransactionJob::dispatch([
                        'transactionable_id' => $booking->id,
                        'transactionable_type' => Booking::class,
                        'amount' => (float) $booking->payment->amount,
                        'converted_amount' => (float) $booking->payment->converted_amount,
                        'currency' => $booking->payment->currency,
                        'converted_currency' => $booking->payment->paid_currency,
                        'exchange_rate' => (float) $booking->payment->exchange_rate,
                        'type' => 'credit',
                        'mode' => $booking->payment->gateway,
                        'note' => 'New Booking',
                        'tax' => 0,
                        'tax_amount' => 0,
                    ]);

                    Cache::forget('rooms_city_'.($booking->hotel->city_id ?? 'all'));
                    // session()->forget('checkoutPayload');
                    // session()->forget('changedStay');
                    // session()->forget('stay');
                    // session()->forget('booking_hotel_id');

                    $user = $booking->user;
                    $user->notify(new BookingConfirmNotification($booking));
                    break;

                case 2:
                    Log::channel('debug')->info('In Case: 2');
                    $booking->update([
                        'status' => Booking::STATUS_FAILED,
                    ]);

                    $booking->payment->update([
                        'status' => Payment::STATUS_FAILED,
                    ]);

                    $admins = User::where('role', 'admin')->get();
                    Notification::send($admins, new AlertAdminPaymentFail($booking->payment));
                    $user = $booking->user;
                    $user->notify(new BookingCancelNotification($booking));
                    break;
                case 3:
                    $booking->update([
                        'status' => Booking::STATUS_PROCESSING,
                    ]);

                    Payment::where('booking_id', $booking->id)->update([
                        'status' => Payment::STATUS_PROCESSING,
                    ]);

                    break;
                case 4:
                    $booking->update([
                        'status' => Booking::STATUS_CANCELLED,
                    ]);

                    $payment = Payment::where('booking_id', $booking->id)->update([
                        'status' => Payment::STATUS_CANCELLED,
                    ]);

                    // if ($booking->discount_id) {
                    //     Discount::findOrFail($booking->discount_id)
                    //         ->decrement('used_count');
                    // }

                    $user = $booking->user;
                    $user->notify(new BookingCancelNotification($booking));
                    break;

                case 5:
                    $booking->update([
                        'status' => Booking::STATUS_REJECTED,
                    ]);

                    $payment = Payment::where('booking_id', $booking->id)->update([
                        'status' => Payment::STATUS_REFUNDED,
                    ]);

                    if ($booking->discount_id) {
                        Discount::findOrFail($booking->discount_id)
                            ->decrement('used_count');
                    }

                    $user = $booking->user;
                    $user->notify(new BookingCancelNotification($booking));
                    break;

                case 6:
                    $booking->update([
                        'status' => Booking::STATUS_REFUNDED,
                    ]);

                    $payment = Payment::where('booking_id', $booking->id)->first();
                    if ($payment) {
                        $payment->update([
                            'status' => Payment::STATUS_REFUNDED,
                        ]);
                    }

                    if ($booking->discount_id) {
                        Discount::findOrFail($booking->discount_id)
                            ->decrement('used_count');
                    }

                    $refund = Refund::firstWhere('booking_id', $booking->id);
                    if ($refund) {
                        $refund->update([
                            'status' => Refund::STATUS_REFUNDED,
                            // 'refund_id' => $paymentIntentId,
                        ]);
                    }


                    CreateTransactionJob::dispatch([
                        'transactionable_id' => $refund->id,
                        'transactionable_type' => Refund::class,
                        'amount' => (float) $booking->payment->amount,
                        'converted_amount' => (float) $booking->payment->converted_amount,
                        'currency' => $booking->payment->currency,
                        'converted_currency' => $booking->payment->paid_currency,
                        'exchange_rate' => (float) $booking->payment->exchange_rate,
                        'type' => 'debit',
                        'mode' => $booking->payment->gateway,
                        'note' => 'New Booking',
                        'tax' => 0,
                        'tax_amount' => 0,
                    ]);

                    // $user = $booking->user;
                    // $user->notify(new BookingCancelNotification($booking));
                    break;

                default:
                    Log::channel('debug')->warning('Invalid Stripe Payment Status Received !! $status = '.$status);
                    $user = User::where('email', 'admin@example.com')->firstOrFail();
                    $user->notify(new AlertAdminPaymentFail($booking->payment));
                    break;
            }

            return true;
        } catch (Exception $e) {
            Log::channel('debug')->error('Error on Finalize method of PaymentService : '.$e->getMessage());

            return false;
        }
    }

    public function cancelBooking(string $sessionId): bool
    {
        return $this->paymentProvider->expireSession($sessionId);
    }

    public function refund(string $payment_id, float $amount, $reason = null, $note = null)
    {
        // return [
        //             'error' => 'Refund Processed Successfully',
        //         ];
        $payment = Payment::findOrFail($payment_id);

        $user_id = auth()->user()->id;

        try {
            $response = $this->paymentProvider->refund($amount, $payment->payment_intent_id);

            return $response;
            if ($response['status']) {

                $refund = Refund::where([
                    'booking_id' => $payment->booking_id,
                    'payment_id' => $payment->id,
                    'user_id' => $user_id,
                ])->first();
                if ($refund) {
                    $refund->update([
                        'refund_id' => $response['data']['refund_id'],
                        'status' => Refund::STATUS_PROCESSING,
                    ]);
                } else {

                    Refund::create([
                        'booking_id' => $payment->booking_id,
                        'payment_id' => $payment->id,
                        'user_id' => $user_id,
                        'refund_id' => $response['data']['refund_id'],
                        'amount' => $amount,
                        'currency' => $response['data']['currency'],
                        'status' => Refund::STATUS_PROCESSING,
                        'reason' => $reason ?? 'Booking Cancelled',
                        'note' => $note,
                    ]);
                }

                return [
                    'success' => 'Refund Processed Successfully',
                ];
            } else {
                return [
                    'error' => 'Something Went Wrong to make Refund',
                ];
            }
        } catch (Exception $e) {
            Log::channel('failures')->critical('Exception on PaymentService refund : '.$e->getMessage());

            return [
                'error' => 'Something Went Wrong to make Refund',
            ];

        }
    }
}
