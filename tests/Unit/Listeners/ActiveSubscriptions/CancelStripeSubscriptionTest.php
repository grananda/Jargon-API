<?php

namespace Tests\Unit\Listeners\ActiveSubscriptions;

use App\Events\ActiveSubscription\ActiveSubscriptionWasDeactivated;
use App\Listeners\CancelStripeSubscription;
use App\Mail\SendSubscriptionDeactivationEmail;
use App\Models\Subscriptions\ActiveSubscription;
use App\Models\User;
use App\Repositories\Stripe\StripeSubscriptionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

/**
 * @group unit
 * @covers \App\Listeners\CancelStripeSubscription
 */
class CancelStripeSubscriptionTest extends TestCase
{
    use RefreshDatabase;
    use CreateActiveSubscription;

    /** @test */
    public function a_stripe_subscription_is_canceled()
    {
        // Given
        Mail::fake();

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $this->createActiveSubscription($user, 'professional-month-eur');

        $this->mock(StripeSubscriptionRepository::class, function ($mock) {
            /* @var \Mockery\Mock $mock */
            $mock->shouldReceive('cancel')
                ->withArgs([User::class, ActiveSubscription::class])
                ->once()
                ->andReturn($this->loadFixture('stripe/subscription.cancel.success'))
            ;
        });

        /** @var \App\Events\ActiveSubscription\ActiveSubscriptionWasDeactivated $event */
        $event = new ActiveSubscriptionWasDeactivated($activeSubscription);

        /** @var \App\Listeners\CancelStripeSubscription $listener */
        $listener = resolve(CancelStripeSubscription::class);

        // When
        $listener->handle($event);

        // Then
        Mail::assertSent(SendSubscriptionDeactivationEmail::class);
    }

    /** @test */
    public function a_stripe_subscription_is_not_canceled()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $this->createActiveSubscription($user, 'professional-month-eur', [], [
            'stripe_id' => null,
        ]);

        $this->mock(StripeSubscriptionRepository::class, function ($mock) {
            /* @var \Mockery\Mock $mock */
            $mock->shouldReceive('cancel')
                ->withArgs([User::class, ActiveSubscription::class])
                ->never()
                ->andReturn($this->loadFixture('stripe/subscription.cancel.success'))
            ;
        });

        /** @var \App\Events\ActiveSubscription\ActiveSubscriptionWasDeactivated $event */
        $event = new ActiveSubscriptionWasDeactivated($activeSubscription);

        /** @var \App\Listeners\CancelStripeSubscription $listener */
        $listener = resolve(CancelStripeSubscription::class);

        // When
        $listener->handle($event);
    }
}
