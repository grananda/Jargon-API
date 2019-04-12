<?php

namespace App\Repositories\Stripe;

use App\Exceptions\StripeApiCallException;
use App\Models\Card;
use App\Models\User;
use Exception;

class StripeCardRepository extends AbstractStripeRepository
{
    /**
     * Adds a Stripe card.
     *
     * @param \App\Models\User $user
     * @param string           $stripeToken
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return mixed
     */
    public function create(User $user, string $stripeToken)
    {
        try {
            return $this->stripe->cards()->create($user->stripe_id, $stripeToken);
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }

    /**
     * Updates a Stripe card.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Card $card
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return mixed
     */
    public function update(User $user, Card $card)
    {
        try {
            return $this->stripe->cards()->update($user->stripe_id, $card->stripe_id, [
                'name'            => $card->name,
                'address_city'    => $card->address_city,
                'address_country' => $card->address_country,
                'address_line1'   => $card->address_line1,
                'address_line2'   => $card->address_line2,
                'address_state'   => $card->address_state,
                'address_zip'     => $card->address_zip,
            ]);
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }

    /**
     * Deletes a Stripe card.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Card $card
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return mixed
     */
    public function delete(User $user, Card $card)
    {
        try {
            /** @var array $response */
            $response = $this->stripe->cards()->delete($user->stripe_id, $card->stripe_id);

            if (! $response['deleted']) {
                throw new StripeApiCallException(trans('Could not delete card.'));
            }

            return true;
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }
}
