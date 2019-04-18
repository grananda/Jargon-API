<?php

namespace App\Services\Contract;

use App\Models\Subscriptions\ActiveSubscription;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\User;

interface SubscriptionServiceInterface
{
    /**
     * Creates a Stripe and active subscription.
     *
     * @param \App\Models\User                           $user
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan
     *
     * @throws \App\Exceptions\ActiveSubscriptionStatusException
     * @throws \App\Exceptions\StripeApiCallException
     * @throws \Throwable
     *
     * @return \App\Models\Subscriptions\ActiveSubscription
     */
    public function subscribe(User $user, SubscriptionPlan $subscriptionPlan): ActiveSubscription;

    /**
     * Cancels an active subscription.
     *
     * @param \App\Models\User                             $user
     * @param \App\Models\Subscriptions\ActiveSubscription $activeSubscription
     *
     * @throws \App\Exceptions\ActiveSubscriptionStatusException
     * @throws \App\Exceptions\StripeApiCallException
     * @throws \Throwable
     *
     * @return \App\Models\Subscriptions\ActiveSubscription
     */
    public function cancelSubscription(User $user, ActiveSubscription $activeSubscription): ActiveSubscription;

    /**
     * Reactivates a canceled subscription.
     *
     * @param \App\Models\User                             $user
     * @param \App\Models\Subscriptions\ActiveSubscription $activeSubscription
     *
     * @return \App\Models\Subscriptions\ActiveSubscription
     */
    public function reactivateSubscription(User $user, ActiveSubscription $activeSubscription): ActiveSubscription;
}
