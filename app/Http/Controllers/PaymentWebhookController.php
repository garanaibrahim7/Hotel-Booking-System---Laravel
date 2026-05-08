<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class PaymentWebhookController extends Controller
{
    public function stripe(Request $request, PaymentService $service)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Log::channel('debug')->info('Webhook Event: ', $request->toArray());
        Log::channel('debug')->info('Webhook Event: '.$event->type);

        $status = match ($event->type) {
            'checkout.session.completed' => 1,
            'payment_intent.payment_failed' => 2,
            'payment_intent.processing' => 3,
            'checkout.session.expired' => 4,
            'payment_intent.canceled' => 4,

            'charge.dispute.created' => 2,
            'charge.refunded' => 6,
            default => null,
        };

        if ($status == 1) {
            Log::channel('debug')->info('Webhook Event: ', $event->data->object->toArray());
        }

        if ($status == null) {
            return response()->json(['message' => 'Event ignored'], 200);
        }

        // if ($status != 1) {
        //     Log::channel('debug')->info('Something Wrong at Payment : ', $event->data->object->toArray());
        // }

        $session = $event->data->object;

        $bookingId = empty($session->metadata->booking_id) ? null : $session->metadata->booking_id;
        $paymentIntentId = empty($session->payment_intent) ? null : $session->payment_intent;

        // if ($status == 6) {
            // Log::channel('debug')->info('Webhook Event: ', $event->toArray());
        // }

        $service->finalizePayment($bookingId, $status, $paymentIntentId);
        // });

        return response()->json(['status' => 'success']);
    }
}
