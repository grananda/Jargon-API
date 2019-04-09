<?php

namespace App\Listeners\SubscriptionPlans;

use App\Events\SubscriptionPlan\SubscriptionPlanWasUpdated;
use App\Repositories\Stripe\StripeSubscriptionPlanRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateStripeSubscriptionPlan implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var \App\Repositories\Stripe\StripeSubscriptionPlanRepository
     */
    private $stripePlanRepository;

    /**
     * CreateStripeSubscriptionPlan constructor.
     *
     * @param \App\Repositories\Stripe\StripeSubscriptionPlanRepository $subscriptionPlanRepository
     */
    public function __construct(StripeSubscriptionPlanRepository $subscriptionPlanRepository)
    {
        $this->stripePlanRepository = $subscriptionPlanRepository;
    }

    /**
     * Handle the event.
     *
     * @param \App\Events\SubscriptionPlan\SubscriptionPlanWasUpdated $event
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return void
     */
    public function handle(SubscriptionPlanWasUpdated $event)
    {
        $this->stripePlanRepository->update($event->subscriptionPlan);
    }
}
