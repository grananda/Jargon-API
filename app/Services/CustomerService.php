<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Stripe\StripeCustomerRepository;

class CustomerService
{
    /**
     * The StripeCustomerRepositoryInstance.
     *
     * @var \App\Repositories\Stripe\StripeCustomerRepository
     */
    private $stripeCustomerRepository;

    /**
     * BillingService constructor.
     *
     * @param \App\Repositories\Stripe\StripeCustomerRepository $stripeCustomerRepository
     */
    public function __construct(StripeCustomerRepository $stripeCustomerRepository)
    {
        $this->stripeCustomerRepository = $stripeCustomerRepository;
    }

    /**
     * Creates a Stripe customer.
     *
     * @param \App\Models\User $user
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return bool
     */
    public function registerCustomer(User $user)
    {
        /** @var array $customer */
        $customer = $this->stripeCustomerRepository->create($user);

        return $user->setStripeId($customer['id']);
    }
}
