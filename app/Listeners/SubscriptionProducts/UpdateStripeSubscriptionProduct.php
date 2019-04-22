<?php

namespace App\Listeners\SubscriptionProducts;

use App\Events\SubscriptionProduct\SubscriptionProductWasUpdated;
use App\Repositories\Stripe\StripeSubscriptionProductRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateStripeSubscriptionProduct implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var \App\Repositories\Stripe\StripeSubscriptionProductRepository
     */
    private $stripeSubscriptionProductRepository;

    /**
     * CreateStripeSubscriptionPlan constructor.
     *
     * @param \App\Repositories\Stripe\StripeSubscriptionProductRepository $stripeSubscriptionProductRepository
     */
    public function __construct(StripeSubscriptionProductRepository $stripeSubscriptionProductRepository)
    {
        $this->stripeSubscriptionProductRepository = $stripeSubscriptionProductRepository;
    }

    /**
     * Handle the event.
     *
     * @param \App\Events\SubscriptionProduct\SubscriptionProductWasUpdated $event
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return void
     */
    public function handle(SubscriptionProductWasUpdated $event)
    {
        $this->stripeSubscriptionProductRepository->update($event->subscriptionProduct);
    }
}
