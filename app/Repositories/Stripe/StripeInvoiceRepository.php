<?php

namespace App\Repositories\Stripe;

use App\Exceptions\StripeApiCallException;
use App\Models\User;
use Exception;

class StripeInvoiceRepository extends AbstractStripeRepository
{
    /**
     * List all invioices per customer.
     *
     * @param \App\Models\User $user
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return mixed
     */
    public function list(User $user)
    {
        try {
            return $this->stripe->invoices()->all([
                'customer' => $user->stripe_id,
            ]);
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }
}
