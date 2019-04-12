<?php

namespace App\Repositories\Stripe;

use App\Exceptions\StripeApiCallException;
use App\Models\Subscriptions\ActiveSubscription;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\User;
use Exception;

class StripeSubscriptionRepository extends AbstractStripeRepository
{
    /**
     * Creates a Stripe subscription.
     *
     * @param \App\Models\User                           $user
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan
     *
     * @return array
     * @throws \App\Exceptions\StripeApiCallException
     *
     */
    public function create(User $user, SubscriptionPlan $subscriptionPlan)
    {
        try {
            return $this->stripe->subscriptions()->create($user->stripe_id, [
                'prorate' => true,
                'items'   => [
                    [
                        'plan' => $subscriptionPlan->alias,
                    ],
                ],
            ]);
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }

    /**
     * Swaps a Stripe subscription.
     *
     * @param \App\Models\User                           $user
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan
     *
     * @return array
     * @throws \App\Exceptions\StripeApiCallException
     *
     */
    public function swap(User $user, SubscriptionPlan $subscriptionPlan)
    {
        try {
            /** @var \App\Models\Subscriptions\SubscriptionPlan $currentSubscriptionPlan */
            $currentSubscriptionPlan = $user->activeSubscription->subscriptionPlan;

            return $this->stripe->subscriptions()->update($user->stripe_id, $currentSubscriptionPlan->alias, [
                'prorate' => true,
                'plan'    => $subscriptionPlan->alias,
            ]);
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }

    /**
     * Cancel sa Stripe subscription.
     *
     * @param \App\Models\User                             $user
     * @param \App\Models\Subscriptions\ActiveSubscription $activeSubscriptionPlan
     *
     * @return array
     * @throws \App\Exceptions\StripeApiCallException
     *
     */
    public function cancel(User $user, ActiveSubscription $activeSubscriptionPlan)
    {
        try {
            return $this->stripe->subscriptions()->cancel($user->stripe_id, $activeSubscriptionPlan->stripe_id, true);
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }

    /**
     * Cancel sa Stripe subscription.
     *
     * @param \App\Models\User                             $user
     * @param \App\Models\Subscriptions\ActiveSubscription $activeSubscriptionPlan
     *
     * @return array
     * @throws \App\Exceptions\StripeApiCallException
     *
     */
    public function reactivate(User $user, ActiveSubscription $activeSubscriptionPlan)
    {
        try {
            return $this->stripe->subscriptions()->reactivate($user->stripe_id, $activeSubscriptionPlan->stripe_id, [
                'cancel_at_period_end' => false,
            ]);
        } catch (Exception $exception) {
            throw new StripeApiCallException($exception);
        }
    }
}
