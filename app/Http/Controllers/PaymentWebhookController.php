<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class PaymentWebhookController extends Controller
{
    public function stripe(Request $request, PaymentService $service, SubscriptionService $subscriptionService)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $session = $event->data->object;

        $subscriptionEvents = [
            'invoice.paid',
            'invoice.payment_failed',
            'customer.subscription.updated',
            'customer.subscription.deleted',
        ];

        $isSubscriptionCheckout = $event->type === 'checkout.session.completed' && isset($session->mode) && $session->mode === 'subscription';

        if (in_array($event->type, $subscriptionEvents) || $isSubscriptionCheckout) {

            $subStatus = match ($event->type) {
                'checkout.session.completed' => 1,
                'customer.subscription.deleted' => 7,
                'invoice.paid' => 8,
                'invoice.payment_failed' => 9,
                'customer.subscription.updated' => 10,
                default => null,
            };

            Log::channel('debug')->info('Routing to Subscription Service: '.$event->type);
            $subscriptionService->finalizeSubscription($session, $subStatus);

            return response()->json(['status' => 'success']);
        }

        $bookingStatus = match ($event->type) {
            'checkout.session.completed' => 1,
            'payment_intent.succeeded' => 1,
            'payment_intent.payment_failed' => 2,
            'payment_intent.processing' => 3,
            'checkout.session.expired' => 4,
            'payment_intent.canceled' => 4,
            'charge.dispute.created' => 5,
            'charge.refunded' => 6,
            default => null,
        };

        if ($bookingStatus == null) {
            return response()->json(['message' => 'Event ignored'], 200);
        }

        // If it makes it here, we are 100% sure it is a hotel booking.
        $bookingId = empty($session->metadata->booking_id) ? null : $session->metadata->booking_id;
        $paymentIntentId = empty($session->payment_intent) ? null : $session->payment_intent;

        $service->finalizePayment($bookingId, $bookingStatus, $paymentIntentId);

        return response()->json(['status' => 'success']);
    }
}
