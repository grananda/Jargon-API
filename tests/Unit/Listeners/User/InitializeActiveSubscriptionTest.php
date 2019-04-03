<?php


namespace Tests\Unit\Listeners\User;

use App\Events\User\UserWasActivated;
use App\Listeners\InitializeActiveSubscription;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Repositories\ActiveSubscriptionRepository;
use App\Repositories\SubscriptionPlanRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InitializeActiveSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function option_users_are_created_when_activating_a_user()
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
}
