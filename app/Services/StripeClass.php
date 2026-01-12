<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\PaymentIntent;
use Exception;

class StripeClass
{
    protected $stripe;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Open a connected account (Create a new Connect account and return onboarding link)
     * 
     * @param string $email
     * @return array
     */
    public function openConnectedAccount(string $email)
    {
        try {
            // 1. Create the account
            $account = Account::create([
                'type' => 'express',
                'email' => $email,
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
            ]);

            // 2. Create the account link for onboarding
            $accountLink = AccountLink::create([
                'account' => $account->id,
                'refresh_url' => route('user.account-dashboard'), // Redirect if link expires
                'return_url' => route('user.account-dashboard'),  // Redirect after completion
                'type' => 'account_onboarding',
            ]);

            return [
                'success' => true,
                'account_id' => $account->id,
                'url' => $accountLink->url
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Create a payment intent
     * 
     * @param float $amount
     * @param string $currency
     * @param array $metadata
     * @return array
     */
    public function createPayment(float $amount, string $currency = 'gbp', array $metadata = [])
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount * 100, // Stripe expects amounts in cents
                'currency' => $currency,
                'metadata' => $metadata,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            return [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
