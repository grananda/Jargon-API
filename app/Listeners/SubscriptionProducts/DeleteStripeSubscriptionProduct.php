<?php

namespace App\Listeners\SubscriptionProducts;

use App\Events\SubscriptionProduct\SubscriptionProductWasDeleted;
use App\Repositories\Stripe\StripeSubscriptionProductRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteStripeSubscriptionProduct implements ShouldQueue
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
     * @param \App\Events\SubscriptionProduct\SubscriptionProductWasDeleted $event
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return void
     */
    public function handle(SubscriptionProductWasDeleted $event)
    {
        $this->stripeSubscriptionProductRepository->delete($event->subscriptionProduct);
    }
}
