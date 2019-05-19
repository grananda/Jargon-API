<?php

namespace Tests\Feature\Webhooks\Stripe;

use App\Events\ActiveSubscription\ActiveSubscriptionWasActivated;
use App\Events\ActiveSubscription\ActiveSubscriptionWasDeactivated;
use App\Mail\SendSubscriptionDeactivationEmail;
use App\Models\Subscriptions\ActiveSubscription;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use Tests\traits\CreateActiveSubscription;

/**
 * @group feature
 * @coversNothing
 */
class InvoicePaymentFailedTest extends AbstractStripeTestCase
{
    use CreateActiveSubscription;

    /** @test */
    public function event_can_be_handled()
    {
        // Given
        Mail::fake();
        Event::fake([
            ActiveSubscriptionWasActivated::class,
            ActiveSubscriptionWasDeactivated::class,
        ]);

        /** @var \App\Models\User $user */
        $user = $this->user('registered-user', [
            'stripe_id' => 'cus_00000000000000',
        ]);

        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = factory(ActiveSubscription::class)->create([
            'stripe_id' => 'sub_00000000000000',
            'user_id'   => $user->id,
        ]);
        $activeSubscription->activate();

        /** @var array $payload */
        $payload = $this->loadFixture('stripe/invoice.payment_failed');

        /** @var array $headers */
        $headers = ['Stripe-Signature' => $this->generateSignature($payload)];

        // When
        $response = $this->postJson(route('webhooks.stripe.index'), $payload, $headers);

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $this->assertFalse($activeSubscription->fresh()->isSubscriptionActive());

        Mail::assertSent(SendSubscriptionDeactivationEmail::class);
        Event::assertDispatched(ActiveSubscriptionWasActivated::class);
        Event::assertDispatched(ActiveSubscriptionWasDeactivated::class);
    }
}
