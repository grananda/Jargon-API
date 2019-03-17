<?php

namespace Tests\traits;


use App\Models\Subscriptions\ActiveSubscription;
use App\Models\Subscriptions\ActiveSubscriptionOptionValue;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\User;

trait CreateActiveSubscription
{
    /**
     * Creates an active subscription.
     *
     * @param \App\Models\User $user
     * @param string           $subscriptionType
     *
     * @param array            $options
     *
     * @return \App\Models\Subscriptions\ActiveSubscription
     */
    public function createActiveSubscription(User $user, string $subscriptionType, array $options = [])
    {
        $this->signIn($user);

        /** @var SubscriptionPlan | null $subscriptionPlan */
        $subscriptionPlan = SubscriptionPlan::findByAliasOrFail($subscriptionType);

        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = factory(ActiveSubscription::class)->create([
            'user_id'              => $user->id,
            'subscription_plan_id' => $subscriptionPlan->id,
            'subscription_active'  => true,
        ]);

        foreach ($subscriptionPlan->options as $option) {
            factory(ActiveSubscriptionOptionValue::class)->create([
                'active_subscription_id' => $activeSubscription->id,
                'option_key'             => $option->option_key,
                'option_value'           => $options[$option->option_key] ?? $option->option_value,
            ]);
        }

        return $activeSubscription;
    }
}
