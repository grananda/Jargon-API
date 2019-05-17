<?php

namespace Tests\Feature\Webhooks\Stripe;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class AbstractStripeTestCase extends TestCase
{
    use RefreshDatabase;

    /**
     * Generate an HMAC Signature.
     *
     * @param array $payload
     *
     * @return string
     */
    protected function generateSignature(array $payload)
    {
        $secret = config('services.stripe.webhook.secret');

        $timestamp = time();

        $signedPayload = $timestamp.'.'.json_encode($payload);

        $signature = hash_hmac('sha256', $signedPayload, $secret);

        return "t={$timestamp},v1={$signature}";
    }
}
