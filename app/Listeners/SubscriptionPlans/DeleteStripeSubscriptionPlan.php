<?php

namespace App\Listeners\SubscriptionPlans;

use App\Events\SubscriptionPlan\SubscriptionPlanWasCreated;
use App\Repositories\Stripe\StripeSubscriptionPlanRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteStripeSubscriptionPlan implements ShouldQueue
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
     * @param \App\Events\SubscriptionPlan\SubscriptionPlanWasCreated $event
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return void
     */
    public function handle(SubscriptionPlanWasCreated $event)
    {
        $this->stripePlanRepository->delete($event->subscriptionPlan);
    }
}
