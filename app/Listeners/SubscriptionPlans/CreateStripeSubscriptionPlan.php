<?php

namespace App\Listeners\SubscriptionPlans;

use App\Events\SubscriptionPlan\SubscriptionPlanWasCreated;
use App\Repositories\Stripe\StripeSubscriptionPlanRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateStripeSubscriptionPlan implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var \App\Repositories\Stripe\StripeSubscriptionPlanRepository
     */
    private $stripeSubscriptionPlanRepository;

    /**
     * CreateStripeSubscriptionPlan constructor.
     *
     * @param \App\Repositories\Stripe\StripeSubscriptionPlanRepository $stripeSubscriptionPlanRepository
     */
    public function __construct(StripeSubscriptionPlanRepository $stripeSubscriptionPlanRepository)
    {
        $this->stripeSubscriptionPlanRepository = $stripeSubscriptionPlanRepository;
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
        $this->stripeSubscriptionPlanRepository->create($event->subscriptionPlan);
    }
}
