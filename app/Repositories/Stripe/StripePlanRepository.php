<?php

namespace App\Repositories\Stripe;

use App\Exceptions\StripeApiCallException;
use App\Exceptions\SubscriptionPlanDeleteException;
use App\Models\Subscriptions\SubscriptionPlan;
use Exception;

class StripePlanRepository
{
    /**
     * @var \Cartalyst\Stripe\Stripe
     */
    private $stripe;

    /**
     * StripePlanRepository constructor.
     *
     * @param \Cartalyst\Stripe\Stripe $stripe
     */
    public function __construct(\Cartalyst\Stripe\Stripe $stripe)
    {
        $this->stripe = $stripe;
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
//        try {
//            return Plan::create([
//                'id'       => $subscriptionPlan->alias,
//                'nickname' => $subscriptionPlan->title.' Monthly Subscription',
//                'active'   => $subscriptionPlan->status,
//                'product'  => [
//                    'id'     => $subscriptionPlan->alias,
//                    'name'   => $subscriptionPlan->title,
//                    'active' => $subscriptionPlan->status,
//                    'type'   => SubscriptionPlan::STANDARD_STRIPE_TYPE_LABEL,
//                ],
//                'amount'     => $subscriptionPlan->amount,
//                'currency'   => Cashier::usesCurrency(),
//                'interval'   => SubscriptionPlan::STANDARD_STRIPE_INTERVAL,
//                'usage_type' => SubscriptionPlan::STANDARD_STRIPE_USAGE_TYPE,
//            ])->jsonSerialize();
//        } catch (Exception $exception) {
//            throw new StripeApiCallException($exception);
//        }
    }

    /**
     * Updates an existing product and plan.
     *
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan
     *
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return array|mixed
     */
    public function update(SubscriptionPlan $subscriptionPlan)
    {
//        try {
//            Product::update($subscriptionPlan->alias, [
//                'name'   => $subscriptionPlan->title,
//                'active' => $subscriptionPlan->status,
//            ]);
//
//            return Plan::update($subscriptionPlan->alias, [
//                'nickname' => $subscriptionPlan->title.' Monthly Subscription',
//                'active'   => $subscriptionPlan->status,
//            ])->jsonSerialize();
//        } catch (Exception $exception) {
//            throw new StripeApiCallException($exception);
//        }
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
//            if ((bool) $subscriptionPlan->activeSubscriptions()->count()) {
//                throw new SubscriptionPlanDeleteException(trans('Cannot delete active subscription.'));
//            }
//
//            /** @var \Stripe\Plan $plan */
//            $plan = Plan::retrieve($subscriptionPlan->alias);
//
//            /** @var \Stripe\Product $product */
//            $product = Product::retrieve($plan->product);
//
//            /** @var \Stripe\ApiResource $response */
//            $response = $plan->delete();
//
//            if (! $response->isDeleted()) {
//                throw new StripeApiCallException(trans('Could not delete subscription plan.'));
//            }
//
//            $response = $product->delete();
//
//            if (! $response->isDeleted()) {
//                throw new StripeApiCallException(trans('Could not delete product.'));
//            }

            return true;
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }
}
