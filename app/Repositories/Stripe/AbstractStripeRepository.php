<?php

namespace App\Repositories\Stripe;

use Cartalyst\Stripe\Stripe;

abstract class AbstractStripeRepository
{
    /**
     * @var \Cartalyst\Stripe\Stripe
     */
    protected $stripe;

    /**
     * StripePlanRepository constructor.
     *
     * @param \Cartalyst\Stripe\Stripe $stripe
     */
    public function __construct(Stripe $stripe)
    {
        $this->stripe = $stripe;
    }
}
