<?php

namespace App\Listeners;

use App\Events\SubscriptionPlan\SubscriptionPlanWasCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateStripeSubscriptionPlan implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param \App\Events\SubscriptionPlan\SubscriptionPlanWasCreated $event
     *
     * @return void
     */
    public function handle(SubscriptionPlanWasCreated $event)
    {

    }
}
