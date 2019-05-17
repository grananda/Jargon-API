<?php

namespace App\Events\SubscriptionPlan;

use App\Models\Subscriptions\SubscriptionPlan;

class SubscriptionPlanWasCreated
{
    /**
     * @var \App\Models\Subscriptions\SubscriptionPlan
     */
    public $subscriptionPlan;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan
     */
    public function __construct(SubscriptionPlan $subscriptionPlan)
    {
        $this->subscriptionPlan = $subscriptionPlan;
    }
}
