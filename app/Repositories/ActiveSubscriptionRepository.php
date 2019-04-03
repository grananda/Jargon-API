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

    public function createActiveSubscription(SubscriptionPlan $subscription, User $user)
    {
        return $this->dbConnection->transaction(function () use ($subscription, $user) {
            /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
            $activeSubscription = $this->create([
                'subscription_active' => true,
            ]);

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
}
