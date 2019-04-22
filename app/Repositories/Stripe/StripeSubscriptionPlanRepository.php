<?php

namespace App\Repositories\Stripe;

use App\Exceptions\StripeApiCallException;
use App\Exceptions\SubscriptionPlanDeleteException;
use App\Models\Subscriptions\SubscriptionPlan;
use Exception;

class StripeSubscriptionPlanRepository extends AbstractStripeRepository
{
    /**
     * Generates a Stripe plan.
     *
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return array
     */
    public function create(SubscriptionPlan $subscriptionPlan)
    {
        try {
            return $this->stripe->plans()->create([
                'id'         => $subscriptionPlan->alias,
                'nickname'   => $subscriptionPlan->title,
                'product'    => $subscriptionPlan->product->alias,
                'amount'     => $subscriptionPlan->amount,
                'currency'   => $subscriptionPlan->currency->code,
                'interval'   => $subscriptionPlan->interval,
                'active'     => $subscriptionPlan->is_active,
                'usage_type' => SubscriptionPlan::STANDARD_STRIPE_USAGE_TYPE,
            ]);
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }

    /**
     * Updates an existing Stripe plan.
     *
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return array
     */
    public function update(SubscriptionPlan $subscriptionPlan)
    {
        try {
            return $this->stripe->plans()->update($subscriptionPlan->alias, [
                'nickname' => $subscriptionPlan->title,
                'active'   => $subscriptionPlan->is_active,
            ]);
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }

    /**
     * Deletes a Stripe plan.
     *
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return bool
     */
    public function delete(SubscriptionPlan $subscriptionPlan)
    {
        try {
            if ((bool) $subscriptionPlan->activeSubscriptions()->count()) {
                throw new SubscriptionPlanDeleteException(trans('Cannot delete active subscription plan.'));
            }

            /** @var array $response */
            $response = $this->stripe->plans()->delete($subscriptionPlan->alias);

            if (! $response['deleted']) {
                throw new StripeApiCallException(trans('Could not delete subscription plan.'));
            }

            return true;
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }
}
