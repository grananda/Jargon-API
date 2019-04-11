<?php

namespace App\Repositories\Stripe;

use App\Exceptions\StripeApiCallException;
use App\Models\User;
use Exception;

class StripeCustomerRepository extends AbstractStripeRepository
{
    /**
     * Creates a Stripe customer.
     *
     * @param \App\Models\User $user
     * @param string|null      $sourceToken
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return array
     */
    public function create(User $user, string $sourceToken = null)
    {
        try {
            $payload = [
                'email'       => $user->email,
                'description' => $user->name,
                'tax_info'    => [
                    'tax_id' => null,
                    'type'   => null,
                ],
            ];

            if ($sourceToken) {
                array_merge($payload, ['source' => $sourceToken]);
            }

            return $this->stripe->customers()->create($payload);
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }

    /**
     * Updates a Stripe customer.
     *
     * @param \App\Models\User $user
     * @param string|null      $sourceToken
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return array
     */
    public function update(User $user, string $sourceToken = null)
    {
        try {
            $payload = [
                'email'       => $user->email,
                'description' => $user->name,
                'tax_info'    => [
                    'tax_id' => null,
                    'type'   => null,
                ],
            ];

            if ($sourceToken) {
                array_merge($payload, ['source' => $sourceToken]);
            }

            return $this->stripe->customers()->update($user->stripe_id, $payload);
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }

    /**
     * Deletes a Stripe customer.
     *
     * @param \App\Models\User $user
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return bool
     */
    public function delete(User $user)
    {
        try {
            /** @var array $response */
            $response = $this->stripe->customers()->delete($user->stripe_id);

            if (! $response['deleted']) {
                throw new StripeApiCallException(trans('Could not delete subscription plan.'));
            }

            return true;
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }
}
