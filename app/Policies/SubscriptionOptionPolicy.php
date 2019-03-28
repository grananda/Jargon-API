<?php

namespace App\Policies;

use App\Models\Subscriptions\SubscriptionOption;
use App\Models\User;

class SubscriptionOptionPolicy extends AbstractPolicy
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
     * @param User                                         $user
     * @param \App\Models\Subscriptions\SubscriptionOption $subscriptionOption
     *
     * @return bool
     */
    public function update(User $user, SubscriptionOption $subscriptionOption)
    {
        return $user->hasRole(User::SENIOR_STAFF_MEMBER);
    }

    /**
     * @param User                                         $user
     * @param \App\Models\Subscriptions\SubscriptionOption $subscriptionOption
     *
     * @return bool
     */
    public function delete(User $user, SubscriptionOption $subscriptionOption)
    {
        return $user->hasRole(User::SENIOR_STAFF_MEMBER);
    }
}
