<?php

namespace App\Repositories;

use App\Exceptions\SubscriptionPlanDeleteException;
use App\Models\Currency;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\Subscriptions\SubscriptionPlanOptionValue;
use App\Models\Subscriptions\SubscriptionProduct;
use Illuminate\Database\Connection;

class SubscriptionPlanRepository extends CoreRepository
{
    /**
     * @var \App\Repositories\SubscriptionOptionRepository
     */
    private $subscriptionOptionRepository;

    /**
     * @var \App\Models\Subscriptions\SubscriptionPlanOptionValue
     */
    private $subscriptionPlanOptionValueRepository;

    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection                         $dbConnection
     * @param \App\Models\Subscriptions\SubscriptionPlan              $model
     * @param \App\Repositories\SubscriptionOptionRepository          $subscriptionOptionRepository
     * @param \App\Repositories\SubscriptionPlanOptionValueRepository $subscriptionPlanOptionValueRepository
     */
    public function __construct(Connection $dbConnection, SubscriptionPlan $model, SubscriptionOptionRepository $subscriptionOptionRepository, SubscriptionPlanOptionValueRepository $subscriptionPlanOptionValueRepository)
    {
        parent::__construct($dbConnection, $model);

        $this->subscriptionOptionRepository          = $subscriptionOptionRepository;
        $this->subscriptionPlanOptionValueRepository = $subscriptionPlanOptionValueRepository;
    }

    /**
     * Creates new model.
     *
     * @param \App\Models\Subscriptions\SubscriptionProduct $product
     * @param \App\Models\Currency                          $currency
     * @param array                                         $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function createSubscriptionPlan(SubscriptionProduct $product, Currency $currency, array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($product, $currency, $attributes) {
            /** @var \App\Models\Subscriptions\SubscriptionPlan $entity */
            $entity = $this->create($attributes);

            $entity->currency()->associate($currency);

            $entity->product()->associate($product);

            $entity->save();

            if (isset($attributes['options'])) {
                foreach ($attributes['options'] as $item) {
                    /** @var \App\Models\Subscriptions\SubscriptionOption $option */
                    if ($option = $this->subscriptionOptionRepository->findBy(['option_key' => $item['option_key']])) {
                        $this->subscriptionPlanOptionValueRepository->createOptionValue($entity, [
                            'option_value' => $item['option_value'],
                            'option_key'   => $option->option_key,
                        ]);
                    }
                }
            }

            return $entity->fresh();
        });
    }

    /**
     * @param \App\Models\Subscriptions\SubscriptionPlan $entity
     * @param array                                      $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function updateSubscriptionPlan(SubscriptionPlan $entity, array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($entity, $attributes) {
            /** @var \App\Models\Subscriptions\SubscriptionPlan $entity */
            $entity = $this->update($entity, $attributes);

            if (isset($attributes['options'])) {
                foreach ($attributes['options'] as $item) {
                    /** @var SubscriptionPlanOptionValue $option */
                    if ($option = $entity->options()->where('option_key', $item['option_key'])->first()) {
                        $this->subscriptionPlanOptionValueRepository->update($option, $item);
                    } /* @var \App\Models\Subscriptions\SubscriptionOption $option */
                    elseif ($option = $this->subscriptionOptionRepository->findBy(['option_key' => $item['option_key']])) {
                        $this->subscriptionPlanOptionValueRepository->createOptionValue($entity, [
                            'option_value' => $item['option_value'],
                            'option_key'   => $option->option_key,
                        ]);
                    }
                }
            }

            return $entity->fresh();
        });
    }

    /**
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan
     *
     * @throws \Throwable
     * @throws \App\Exceptions\SubscriptionPlanDeleteException
     *
     * @return mixed
     */
    public function deleteSubscriptionPlan(SubscriptionPlan $subscriptionPlan)
    {
        if ((bool) $subscriptionPlan->activeSubscriptions()->count()) {
            throw new SubscriptionPlanDeleteException(trans('Cannot delete active subscription.'));
        }

        return $this->dbConnection->transaction(function () use ($subscriptionPlan) {
            /* @var SubscriptionPlan $entity */
            return $this->delete($subscriptionPlan);
        });
    }
}
