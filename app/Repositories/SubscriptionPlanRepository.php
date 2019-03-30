<?php

namespace App\Repositories;

use App\Models\Subscriptions\SubscriptionPlan;
use Illuminate\Database\Connection;

class SubscriptionPlanRepository extends CoreRepository
{
    /**
     * @var \App\Repositories\SubscriptionOptionRepository
     */
    private $subscriptionOptionRepository;

    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection                $dbConnection
     * @param \App\Models\Subscriptions\SubscriptionPlan     $model
     * @param \App\Repositories\SubscriptionOptionRepository $subscriptionOptionRepository
     */
    public function __construct(Connection $dbConnection, SubscriptionPlan $model, SubscriptionOptionRepository $subscriptionOptionRepository)
    {
        parent::__construct($dbConnection, $model);

        $this->subscriptionOptionRepository = $subscriptionOptionRepository;
    }

    /**
     * Creates new model.
     *
     * @param array $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function createSubscriptionPlan(array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($attributes) {
            /** @var \App\Models\Subscriptions\SubscriptionPlan $entity */
            $entity = $this->create($attributes);

            if (isset($attributes['options'])) {
                foreach ($attributes['options'] as $item) {
                    /** @var \App\Models\Subscriptions\SubscriptionOption $option */
                    if ($option = $this->subscriptionOptionRepository->findBy(['option_key' => $item['option_key']])) {
                        $entity->addOption($option, $item['option_value']);
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
                    if ($option = $entity->options()->where('option_key', $item['option_key'])->first()) {
                        $option->option_value = $item['option_value'];
                        $option->save();
                    } /* @var \App\Models\Subscriptions\SubscriptionOption $option */
                    elseif ($option = $this->subscriptionOptionRepository->findBy(['option_key' => $item['option_key']])) {
                        $entity->addOption($option, $item['option_value']);
                    }
                }
            }

            return $entity->fresh();
        });
    }
}
