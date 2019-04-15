<?php


namespace App\Models;


trait HasStripeId
{
    /**
     * Sets user Stripe Id.
     *
     * @param string $stripeId
     *
     * @return bool
     */
    public function setStripeId(string $stripeId)
    {
        $this->update(['stripe_id' => $stripeId]);

        return $this->refresh();
    }
}