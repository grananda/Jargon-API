<?php

namespace Tests\Unit\Listeners\User;

use App\Events\ActiveSubscription\ActiveSubscriptionWasActivated;
use App\Events\User\UserWasActivated;
use App\Listeners\InitializeActiveSubscription;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Repositories\ActiveSubscriptionRepository;
use App\Repositories\SubscriptionPlanRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

/**
 * @group unit
 * @coversNothing
 */
class InitializeActiveSubscriptionTest extends TestCase
{
    use RefreshDatabase;
    use CreateActiveSubscription;

    /** @test */
    public function active_subscription_is_created_when_activating_a_user()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscription */
        $subscription = SubscriptionPlan::where('alias', SubscriptionPlan::DEFAULT_SUBSCRIPTION_PLAN)->first();

        /** @var \App\Events\User\UserWasActivated $event */
        $event = new UserWasActivated($user);

        /** @var SubscriptionPlanRepository $subscriptionPlanRepository */
        $subscriptionPlanRepository = resolve(SubscriptionPlanRepository::class);

        /** @var ActiveSubscriptionRepository $activeSubscriptionPlanRepository */
        $activeSubscriptionRepository = resolve(ActiveSubscriptionRepository::class);

        /** @var \App\Listeners\InitializeUserOptions $listener */
        $listener = new InitializeActiveSubscription($subscriptionPlanRepository, $activeSubscriptionRepository);

        // When
        $listener->handle($event);

        $user = $user->fresh();

        // Then
        $this->assertSame($user->activeSubscription->subscriptionPlan->id, $subscription->id);
        $this->assertSame($user->activeSubscription->options()->count(), $subscription->options()->count());
    }

    /** @test */
    public function active_subscription_is_activated_when_activating_a_user()
    {
        // Given
        Event::fake(ActiveSubscriptionWasActivated::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscription */
        $subscription = $this->createActiveSubscription($user, SubscriptionPlan::DEFAULT_SUBSCRIPTION_PLAN, [
            'subscription_active' => false,
            'stripe_id'           => null,
        ]);

        /** @var \App\Events\User\UserWasActivated $event */
        $event = new UserWasActivated($user);

        /** @var SubscriptionPlanRepository $subscriptionPlanRepository */
        $subscriptionPlanRepository = resolve(SubscriptionPlanRepository::class);

        /** @var ActiveSubscriptionRepository $activeSubscriptionPlanRepository */
        $activeSubscriptionRepository = resolve(ActiveSubscriptionRepository::class);

        /** @var \App\Listeners\InitializeUserOptions $listener */
        $listener = new InitializeActiveSubscription($subscriptionPlanRepository, $activeSubscriptionRepository);

        // When
        $listener->handle($event);

        $user = $user->fresh();

        // Then
        $this->assertSame($user->activeSubscription->subscriptionPlan->id, $subscription->id);
        $this->assertTrue($user->activeSubscription->subscription_active);
        $this->assertSame($user->activeSubscription->options()->count(), $subscription->options()->count());

        Event::assertDispatched(ActiveSubscriptionWasActivated::class);
    }
}
