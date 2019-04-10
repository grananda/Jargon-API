<?php

namespace App\Repositories;

use App\Exceptions\SubscriptionProductDeleteException;
use App\Models\Subscriptions\SubscriptionProduct;
use Illuminate\Database\Connection;

class SubscriptionProductRepository extends CoreRepository
{
    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection               $dbConnection
     * @param \App\Models\Subscriptions\SubscriptionProduct $model
     */
    public function __construct(Connection $dbConnection, SubscriptionProduct $model)
    {
        parent::__construct($dbConnection, $model);
    }

    /**
     * @param \App\Models\Subscriptions\SubscriptionProduct $subscriptionProduct
     *
     * @throws \App\Exceptions\SubscriptionProductDeleteException
     * @throws \Throwable
     *
     * @return mixed
     */
    public function deleteSubscriptionProduct(SubscriptionProduct $subscriptionProduct)
    {
        if ((bool) $subscriptionProduct->plans()->count()) {
            throw new SubscriptionProductDeleteException(trans('Cannot delete active product.'));
        }

        return $this->dbConnection->transaction(function () use ($subscriptionProduct) {
            /* @var $subscriptionProduct $entity */
            return $this->delete($subscriptionProduct);
        });
    }
}
