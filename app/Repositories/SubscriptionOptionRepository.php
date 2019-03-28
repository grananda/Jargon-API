<?php

namespace App\Repositories;

use App\Models\Subscriptions\SubscriptionOption;
use Illuminate\Database\Connection;

class SubscriptionOptionRepository extends CoreRepository
{
    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection              $dbConnection
     * @param \App\Models\Subscriptions\SubscriptionOption $model
     */
    public function __construct(Connection $dbConnection, SubscriptionOption $model)
    {
        parent::__construct($dbConnection, $model);
    }
}
