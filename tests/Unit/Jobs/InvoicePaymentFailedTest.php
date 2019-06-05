<?php

namespace Tests\Unit\Jobs;

use App\Events\ActiveSubscription\ActiveSubscriptionWasActivated;
use App\Events\ActiveSubscription\ActiveSubscriptionWasDeactivated;
use App\Jobs\Stripe\InvoicePaymentFailed;
use App\Mail\SendSubscriptionDeactivationEmail;
use App\Models\Subscriptions\ActiveSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * @group unit
 * @covers \App\Jobs\Stripe\InvoicePaymentFailed
 */
class InvoicePaymentFailedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function job_gets_dispatched_with_valid_data()
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

        // When
        InvoicePaymentFailed::dispatch($payload['data']['object']);

        // Then
        Event::assertDispatched(ActiveSubscriptionWasActivated::class);
        Event::assertDispatched(ActiveSubscriptionWasDeactivated::class);
        Mail::assertSent(SendSubscriptionDeactivationEmail::class);
        $this->assertFalse($activeSubscription->fresh()->isSubscriptionActive());
    }

    /** @test */
    public function ensure_the_tags_are_properly_defined()
    {
        // Given
        Queue::fake();

        $payload = $this->loadFixture('stripe/invoice.payment_failed');

        InvoicePaymentFailed::dispatch($payload['data']['object']);

        Queue::assertPushed(InvoicePaymentFailed::class, 1);

        Queue::assertPushed(InvoicePaymentFailed::class, function ($job) use ($payload) {
            $customerId = $payload['data']['object']['customer'];

            return $job->tags() === ['stripe', "stripe:{$customerId}"];
        });
    }
}
