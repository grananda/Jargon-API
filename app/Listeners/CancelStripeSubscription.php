<?php

namespace App\Listeners;

use App\Events\ActiveSubscription\ActiveSubscriptionWasDeactivated;
use App\Repositories\Stripe\StripeSubscriptionRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CancelStripeSubscription implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var \App\Repositories\Stripe\StripeSubscriptionRepository
     */
    private $stripeSubscriptionRepository;

    /**
     * CancelStripeSubscription constructor.
     *
     * @param \App\Repositories\Stripe\StripeSubscriptionRepository $stripeSubscriptionRepository
     */
    public function __construct(StripeSubscriptionRepository $stripeSubscriptionRepository)
    {
        $this->stripeSubscriptionRepository = $stripeSubscriptionRepository;
    }

    /**
     * Handle the event.
     *
     * @param \App\Events\ActiveSubscription\ActiveSubscriptionWasDeactivated $event
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return void
     */
    public function handle(ActiveSubscriptionWasDeactivated $event)
    {
        if (isset($event->activeSubscription->stripe_id)) {
            $this->stripeSubscriptionRepository->cancel($event->activeSubscription->user, $event->activeSubscription);
        }
    }
}
