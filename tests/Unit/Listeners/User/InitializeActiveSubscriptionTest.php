<?php


namespace Tests\Unit\Listeners\User;

use App\Events\User\UserWasActivated;
use App\Listeners\InitializeActiveSubscription;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Repositories\ActiveSubscriptionRepository;
use App\Repositories\SubscriptionPlanRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

class InitializeActiveSubscriptionTest extends TestCase
{
    use RefreshDatabase,
        CreateActiveSubscription;

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
        $this->assertEquals($user->activeSubscription->subscriptionPlan->id, $subscription->id);
        $this->assertEquals($user->activeSubscription->options()->count(), $subscription->options()->count());
    }

    /** @test */
    public function active_subscription_is_activated_when_activating_a_user()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscription */
        $subscription = $this->createActiveSubscription($user, SubscriptionPlan::DEFAULT_SUBSCRIPTION_PLAN, ['subscription_active' => false]);

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
        $this->assertEquals($user->activeSubscription->subscriptionPlan->id, $subscription->id);
        $this->assertTrue($user->activeSubscription->subscription_active);
        $this->assertEquals($user->activeSubscription->options()->count(), $subscription->options()->count());
    }
}
