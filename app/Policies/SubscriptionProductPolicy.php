<?php

namespace App\Policies;

use App\Models\Subscriptions\SubscriptionProduct;
use App\Models\User;

class SubscriptionProductPolicy extends \AbstractSeeder
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
     * @param User                                          $user
     * @param \App\Models\Subscriptions\SubscriptionProduct $subscriptionProduct
     *
     * @return bool
     */
    public function update(User $user, SubscriptionProduct $subscriptionProduct)
    {
        return $user->hasRole(User::SENIOR_STAFF_MEMBER);
    }

    /**
     * @param User                                          $user
     * @param \App\Models\Subscriptions\SubscriptionProduct $subscriptionProduct
     *
     * @return bool
     */
    public function delete(User $user, SubscriptionProduct $subscriptionProduct)
    {
        return $user->hasRole(User::SENIOR_STAFF_MEMBER) && $subscriptionProduct->plans()->count() === 0;
    }
}
