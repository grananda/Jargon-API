<?php

namespace Tests\Unit\Listeners\User;

use App\Events\ActiveSubscription\ActiveSubscriptionWasDeactivated;
use App\Events\User\UserWasDeactivated;
use App\Listeners\DeactivateActiveSubscription;
use App\Models\Subscriptions\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

/**
 * @group unit
 * @covers \App\Events\ActiveSubscription\ActiveSubscriptionWasDeactivated
 */
class DeactivateActiveSubscriptionTest extends TestCase
{
    use RefreshDatabase;
    use CreateActiveSubscription;

    /** @test */
    public function user_active_subscription_is_deactivated()
    {
        // Given
        Event::fake(ActiveSubscriptionWasDeactivated::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Subscriptions\ActiveSubscription $subscription */
        $subscription = $this->createActiveSubscription($user, SubscriptionPlan::DEFAULT_SUBSCRIPTION_PLAN);

        /** @var \App\Events\User\UserWasActivated $event */
        $event = new UserWasDeactivated($user);

        /** @var \App\Listeners\DeactivateActiveSubscription $listener */
        $listener = new DeactivateActiveSubscription();

        // When
        $listener->handle($event);

        // Then
        $this->assertFalse($user->fresh()->activeSubscription->subscription_active);
        $this->assertFalse($subscription->fresh()->subscription_active);

        Event::assertDispatched(ActiveSubscriptionWasDeactivated::class);
    }
}
