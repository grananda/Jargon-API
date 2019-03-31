<?php

namespace App\Policies;

use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\User;

class SubscriptionPlanPolicy extends AbstractPolicy
{
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
}
