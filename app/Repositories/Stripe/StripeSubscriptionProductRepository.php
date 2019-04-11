<?php

namespace App\Repositories\Stripe;

use App\Exceptions\StripeApiCallException;
use App\Exceptions\SubscriptionPlanDeleteException;
use App\Models\Subscriptions\SubscriptionProduct;
use Exception;

class StripeSubscriptionProductRepository extends AbstractStripeRepository
{
    /**
     * Creates a Stripe product.
     *
     * @param \App\Models\Subscriptions\SubscriptionProduct $subscriptionProduct
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return array
     */
    public function create(SubscriptionProduct $subscriptionProduct)
    {
        try {
            return $this->stripe->products()->create([
                'id'     => $subscriptionProduct->alias,
                'name'   => $subscriptionProduct->title,
                'type'   => SubscriptionProduct::STANDARD_STRIPE_TYPE_LABEL,
                'active' => (bool) $subscriptionProduct->is_active,
            ]);
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }

    /**
     * Updates a Stripe product.
     *
     * @param \App\Models\Subscriptions\SubscriptionProduct $subscriptionProduct
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return array
     */
    public function update(SubscriptionProduct $subscriptionProduct)
    {
        try {
            return $this->stripe->products()->update($subscriptionProduct->alias, [
                'name'   => $subscriptionProduct->title,
                'active' => (bool) $subscriptionProduct->is_active,
            ]);
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }

    /**
     * Deletes a Stripe product.
     *
     * @param \App\Models\Subscriptions\SubscriptionProduct $subscriptionProduct
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return bool
     */
    public function delete(SubscriptionProduct $subscriptionProduct)
    {
        try {
            if ((bool) $subscriptionProduct->plans()->count()) {
                throw new SubscriptionPlanDeleteException(trans('Cannot delete active subscription product.'));
            }

            /** @var array $response */
            $response = $this->stripe->products()->delete($subscriptionProduct->alias);

            if (! $response['deleted']) {
                throw new StripeApiCallException(trans('Could not delete subscription product.'));
            }

            return true;
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }
}
