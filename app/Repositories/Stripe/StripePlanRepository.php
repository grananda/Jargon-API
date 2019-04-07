<?php

namespace App\Repositories\Stripe;

use App\Exceptions\StripeApiCallException;
use App\Exceptions\SubscriptionPlanDeleteException;
use App\Models\Subscriptions\SubscriptionPlan;
use Exception;
use Laravel\Cashier\Cashier;
use Stripe\Plan;
use Stripe\Product;
use Stripe\Stripe;

class StripePlanRepository
{
    /**
     * StripeApiGateway constructor.
     */
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Generates a Stripe product and plan.
     *
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return \Stripe\ApiResource
     */
    public function create(SubscriptionPlan $subscriptionPlan)
    {
        try {
            return Plan::create([
                'id'       => $subscriptionPlan->alias,
                'nickname' => $subscriptionPlan->title.' Monthly Subscription',
                'product'  => [
                    'name' => $subscriptionPlan->title,
                    'type' => SubscriptionPlan::STANDARD_STRIPE_TYPE_LABEL,
                ],
                'amount'     => $subscriptionPlan->amount,
                'currency'   => Cashier::usesCurrency(),
                'interval'   => SubscriptionPlan::STANDARD_STRIPE_BILLING_PERIOD,
                'usage_type' => SubscriptionPlan::STANDARD_STRIPE_BILLING_USAGE_TYPE,
            ])->jsonSerialize();
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }

    /**
     * Deletes a Stripe product and plan.
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
                throw new SubscriptionPlanDeleteException(trans('Cannot delete active subscription.'));
            }

            /** @var \Stripe\Plan $plan */
            $plan = Plan::retrieve($subscriptionPlan->alias);

            /** @var \Stripe\Product $product */
            $product = Product::retrieve($plan->product);

            /** @var \Stripe\ApiResource $response */
            $response = $plan->delete();

            if (! $response->isDeleted()) {
                throw new StripeApiCallException(trans('Could not delete subscription plan.'));
            }

            $response = $product->delete();

            if (! $response->isDeleted()) {
                throw new StripeApiCallException(trans('Could not delete product.'));
            }

            return true;
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }
}
