<?php

namespace App\Jobs;

use App\Models\User;
use App\Repositories\Stripe\StripeCustomerRepository;

class UpdateStripeCustomer extends AbstractJob
{
    /**
     * @var \App\Models\User
     */
    private $user;

    /**
     * UpdateStripeCustomer constructor.
     *
     * @param \App\Models\User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @param \App\Repositories\Stripe\StripeCustomerRepository $stripeCustomerRepository
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return void
     */
    public function handle(StripeCustomerRepository $stripeCustomerRepository)
    {
        $stripeCustomerRepository->update($this->user);
    }
}
