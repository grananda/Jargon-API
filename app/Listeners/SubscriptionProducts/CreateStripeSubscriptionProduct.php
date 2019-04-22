<?php

namespace App\Listeners\SubscriptionProducts;

use App\Events\SubscriptionProduct\SubscriptionProductWasCreated;
use App\Repositories\Stripe\StripeSubscriptionProductRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateStripeSubscriptionProduct implements ShouldQueue
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
     * @param \App\Events\SubscriptionProduct\SubscriptionProductWasCreated $event
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return void
     */
    public function handle(SubscriptionProductWasCreated $event)
    {
        $this->stripeSubscriptionProductRepository->create($event->subscriptionProduct);
    }
}
