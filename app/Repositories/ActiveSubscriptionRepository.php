<?php

namespace App\Repositories;

use App\Models\Subscriptions\ActiveSubscription;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Connection;

class ActiveSubscriptionRepository extends CoreRepository
{
    /**
     * @var \App\Repositories\SubscriptionPlanRepository
     */
    private $subscriptionPlanRepository;

    /**
     * @var \App\Repositories\ActiveSubscriptionPlanOptionValueRepository
     */
    private $activeSubscriptionPlanOptionValueRepository;

    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection                               $dbConnection
     * @param \App\Models\Subscriptions\ActiveSubscription                  $model
     * @param \App\Repositories\SubscriptionPlanRepository                  $subscriptionPlanRepository
     * @param \App\Repositories\ActiveSubscriptionPlanOptionValueRepository $activeSubscriptionPlanOptionValueRepository
     */
    public function __construct(Connection $dbConnection,
                                ActiveSubscription $model,
                                SubscriptionPlanRepository $subscriptionPlanRepository,
                                ActiveSubscriptionPlanOptionValueRepository $activeSubscriptionPlanOptionValueRepository
    ) {
        parent::__construct($dbConnection, $model);

        $this->subscriptionPlanRepository                  = $subscriptionPlanRepository;
        $this->activeSubscriptionPlanOptionValueRepository = $activeSubscriptionPlanOptionValueRepository;
    }

    /**
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscription
     * @param \App\Models\User                           $user
     * @param array                                      $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function createActiveSubscription(SubscriptionPlan $subscription, User $user, array $attributes = [])
    {
        return $this->dbConnection->transaction(function () use ($subscription, $user, $attributes) {
            /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
            $activeSubscription = $this->create(array_merge([
                'subscription_active' => true,
            ], $attributes));

            $subscription->activeSubscriptions()->save($activeSubscription);

            $user->activeSubscription()->save($activeSubscription);

            /** @var \App\Models\Subscriptions\SubscriptionPlanOptionValue $option */
            foreach ($subscription->options as $option) {
                /** @var \App\Models\Subscriptions\SubscriptionPlanOptionValue $optionValue */
                $optionValue = $this->activeSubscriptionPlanOptionValueRepository->createActiveSubscriptionPlanOptionValue($activeSubscription, [
                    'option_key'   => $option->option_key,
                    'option_value' => $option->option_value,
                ]);
            }

            return $activeSubscription->fresh();
        });
    }

    /**
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscription
     * @param \App\Models\User                           $user
     * @param array                                      $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function updateActiveSubscription(SubscriptionPlan $subscription, User $user, array $attributes = [])
    {
        return $this->dbConnection->transaction(function () use ($subscription, $user, $attributes) {
            /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
            $activeSubscription = $user->activeSubscription;

            $activeSubscription->fill($attributes);

            $subscription->activeSubscriptions()->save($activeSubscription);

            /** @var \App\Models\Subscriptions\SubscriptionPlanOptionValue $option */
            foreach ($subscription->options as $option) {
                /** @var \App\Models\Subscriptions\SubscriptionPlanOptionValue $optionValue */
                $optionValue = $this->activeSubscriptionPlanOptionValueRepository->updateActiveSubscriptionPlanOptionValue($activeSubscription, [
                    'option_key'   => $option->option_key,
                    'option_value' => $option->option_value,
                ]);
            }

            return $activeSubscription->fresh();
        });
    }
}
