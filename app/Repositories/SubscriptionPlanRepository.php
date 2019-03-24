<?php

namespace App\Repositories;

use App\Models\Subscriptions\SubscriptionPlan;
use Illuminate\Database\Connection;

class SubscriptionPlanRepository extends CoreRepository
{
    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection            $dbConnection
     * @param \App\Models\Subscriptions\SubscriptionPlan $model
     */
    public function __construct(Connection $dbConnection, SubscriptionPlan $model)
    {
        parent::__construct($dbConnection, $model);
    }
}
