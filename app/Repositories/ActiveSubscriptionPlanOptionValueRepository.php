<?php

namespace App\Repositories;

use App\Models\Subscriptions\ActiveSubscription;
use App\Models\Subscriptions\ActiveSubscriptionOptionValue;
use Illuminate\Database\Connection;

class ActiveSubscriptionPlanOptionValueRepository extends CoreRepository
{
    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection                         $dbConnection
     * @param \App\Models\Subscriptions\ActiveSubscriptionOptionValue $model
     */
    public function __construct(Connection $dbConnection, ActiveSubscriptionOptionValue $model)
    {
        parent::__construct($dbConnection, $model);
    }

    /**
     * @param \App\Models\Subscriptions\ActiveSubscription $activeSubscription
     * @param array                                        $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function createActiveSubscriptionPlanOptionValue(ActiveSubscription $activeSubscription, array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($activeSubscription, $attributes) {
            /** @var \App\Models\Subscriptions\ActiveSubscriptionOptionValue $entity */
            $entity = $this->create($attributes);

            $activeSubscription->options()->save($entity);

            return $entity->fresh();
        });
    }
}
