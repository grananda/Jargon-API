<?php

namespace App\Repositories;

use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\Subscriptions\SubscriptionPlanOptionValue;
use Illuminate\Database\Connection;

class SubscriptionPlanOptionValueRepository extends CoreRepository
{
    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection                       $dbConnection
     * @param \App\Models\Subscriptions\SubscriptionPlanOptionValue $model
     */
    public function __construct(Connection $dbConnection, SubscriptionPlanOptionValue $model)
    {
        parent::__construct($dbConnection, $model);
    }

    public function createOptionValue(SubscriptionPlan $subscriptionPlan, array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($attributes, $subscriptionPlan) {
            /** @var SubscriptionPlanOptionValue $entity */
            $entity = $this->create($attributes);

            $subscriptionPlan->addOption($entity);

            return $entity;
        });
    }
}
