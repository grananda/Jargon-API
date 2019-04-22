<?php

namespace App\Events\ActiveSubscription;

use App\Models\Subscriptions\ActiveSubscription;

class ActiveSubscriptionWasActivated
{
    /**
     * @var \App\Models\Subscriptions\ActiveSubscription
     */
    public $activeSubscription;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Subscriptions\ActiveSubscription $activeSubscription
     */
    public function __construct(ActiveSubscription $activeSubscription)
    {
        $this->activeSubscription = $activeSubscription;
    }
}
