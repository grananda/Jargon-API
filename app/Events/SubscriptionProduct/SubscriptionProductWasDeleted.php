<?php

namespace App\Events\SubscriptionProduct;

use App\Models\Subscriptions\SubscriptionProduct;

class SubscriptionProductWasDeleted
{
    /**
     * @var \App\Models\Subscriptions\SubscriptionProduct
     */
    public $subscriptionProduct;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Subscriptions\SubscriptionProduct $subscriptionPlan
     */
    public function __construct(SubscriptionProduct $subscriptionPlan)
    {
        $this->subscriptionProduct = $subscriptionPlan;
    }
}
