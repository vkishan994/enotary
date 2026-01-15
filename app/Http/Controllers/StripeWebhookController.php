<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Stripe::setApiKey(getValuesByKey('stripe_secret_key'));

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = getValuesByKey('stripe_webhook_secret');

        try {
            // Verify webhook signature
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle events
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;

            // Add more events if needed
            default:
                Log::info('Unhandled Stripe event: ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }

    protected function handlePaymentSucceeded($paymentIntent)
    {
        // Update the corresponding order
        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();
        if ($order) {
            $order->payment_status = 'completed';
            $order->save();
        }

        // Write to custom log channel
        Log::channel('stripe_webhook')->info('Payment succeeded', [
            'order_id' => $order->id ?? null,
            'amount_received' => $paymentIntent->amount_received,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    protected function handlePaymentFailed($paymentIntent)
    {
        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();
        if ($order) {
            $order->payment_status = 'failed';
            $order->save();
        }

        Log::channel('stripe_webhook')->warning('Payment failed', [
            'order_id' => $order->id ?? null,
            'stripe_payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
            'currency' => $paymentIntent->currency,
            'failure_message' => $paymentIntent->last_payment_error->message ?? null,
            'timestamp' => now()->toDateTimeString()
        ]);
    }
}
