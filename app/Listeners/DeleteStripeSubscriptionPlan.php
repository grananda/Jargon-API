<?php

namespace App\Listeners;

use App\Events\SubscriptionPlan\SubscriptionPlanWasCreated;
use App\Repositories\Stripe\StripePlanRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteStripeSubscriptionPlan implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var \App\Repositories\Stripe\StripePlanRepository
     */
    private $stripePlanRepository;

    /**
     * CreateStripeSubscriptionPlan constructor.
     *
     * @param \App\Repositories\Stripe\StripePlanRepository $stripeApiGateway
     */
    public function __construct(StripePlanRepository $stripeApiGateway)
    {
        $this->stripePlanRepository = $stripeApiGateway;
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
