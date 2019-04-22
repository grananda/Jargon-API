<?php

namespace App\Policies;

use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\User;
use App\Policies\Traits\ActiveSubscriptionRestrictionsTrait;

class SubscriptionPlanPolicy extends AbstractPolicy
{
    use ActiveSubscriptionRestrictionsTrait;

    /**
     * @param User $user
     *
     * @return bool
     */
    public function list(User $user)
    {
        return true;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasRole(User::SENIOR_STAFF_MEMBER);
    }

    /**
     * @param User                                       $user
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan
     *
     * @return bool
     */
    public function update(User $user, SubscriptionPlan $subscriptionPlan)
    {
        return $user->hasRole(User::SENIOR_STAFF_MEMBER);
    }

    /**
     * @param User                                       $user
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan
     *
     * @return bool
     */
    public function delete(User $user, SubscriptionPlan $subscriptionPlan)
    {
        return $user->hasRole(User::SENIOR_STAFF_MEMBER) && $subscriptionPlan->activeSubscriptions()->count() === 0;
    }

    /**
     * @param \App\Models\User                           $user
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan
     *
     * @return bool
     */
    public function upgrade(User $user, SubscriptionPlan $subscriptionPlan)
    {
        return $this->hasActiveSubscription($user)
            && $this->canUpgrade($user, $subscriptionPlan);
    }

    /**
     * @param \App\Models\User                           $user
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan
     *
     * @return bool
     */
    public function downgrade(User $user, SubscriptionPlan $subscriptionPlan)
    {
        return $this->hasActiveSubscription($user)
            && $this->canDowngrade($user, $subscriptionPlan);
    }
}
