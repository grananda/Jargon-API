<?php

namespace Tests\Feature\Webhooks\Stripe;

use Symfony\Component\HttpFoundation\Response;

/**
 * @group feature
 * @covers \App\Http\Controllers\Webhook\StripeWebHookController::index
 */
class StripeTest extends AbstractStripeTestCase
{
    /** @test */
    public function a_403_will_be_returned_if_the_request_is_not_genuine()
    {
        // Arrange
        $headers = ['Stripe-Signature' => '123456789'];

        // Act
        $response = $this->postJson(route('webhooks.stripe.index'), [], $headers);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertSeeText('Signature verification failed!');
    }

    /** @test */
    public function a_400_will_be_returned_when_the_payload_is_invalid()
    {
        // Arrange
        $payload = [];

        $headers = ['Stripe-Signature' => $this->generateSignature($payload)];

        // Act
        $response = $this->postJson(route('webhooks.stripe.index'), $payload, $headers);

        // Assert
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertSeeText('Payload is invalid!');
    }
}
