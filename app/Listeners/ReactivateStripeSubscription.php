<?php

namespace App\Listeners;

use App\Events\ActiveSubscription\ActiveSubscriptionWasActivated;
use App\Mail\SendSubscriptionActivationEmail;
use App\Repositories\Stripe\StripeSubscriptionRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class ReactivateStripeSubscription implements ShouldQueue
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
     * @param \App\Events\ActiveSubscription\ActiveSubscriptionWasActivated $event
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return void
     */
    public function handle(ActiveSubscriptionWasActivated $event)
    {
        if (isset($event->activeSubscription->stripe_id)) {
            $this->stripeSubscriptionRepository->reactivate($event->activeSubscription->user, $event->activeSubscription);

            Mail::to($event->activeSubscription->user)
                ->send(new SendSubscriptionActivationEmail($event->activeSubscription))
            ;
        }
    }
}
