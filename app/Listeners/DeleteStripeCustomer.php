<?php

namespace App\Listeners;

use App\Events\User\UserWasDeleted;
use App\Repositories\Stripe\StripeCustomerRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteStripeCustomer implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var \App\Repositories\Stripe\StripeCustomerRepository
     */
    private $stripeCustomerRepository;

    /**
     * DeleteStripeCustomer constructor.
     *
     * @param \App\Repositories\Stripe\StripeCustomerRepository $stripeCustomerRepository
     */
    public function __construct(StripeCustomerRepository $stripeCustomerRepository)
    {
        $this->stripeCustomerRepository = $stripeCustomerRepository;
    }

    /**
     * Handle the event.
     *
     * @param \App\Events\User\UserWasDeleted $event
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return void
     */
    public function handle(UserWasDeleted $event)
    {
        if ($event->user->stripe_id) {
            $this->stripeCustomerRepository->delete($event->user);
        }
    }
}
