<?php

namespace App\Repositories;

use App\Models\Card;
use App\Models\User;
use Illuminate\Database\Connection;

class CardRepository extends CoreRepository
{
    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection $dbConnection
     * @param \App\Models\Card                $model
     */
    public function __construct(Connection $dbConnection, Card $model)
    {
        parent::__construct($dbConnection, $model);
    }

    /**
     * Creates a local credit card.
     *
     * @param \App\Models\User $user
     * @param array            $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function createCreditCard(User $user, array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($user, $attributes) {
            /** @var \App\Models\Card $entity */
            $entity = $this->create($attributes);

            $entity->user()->associate($user);

            $entity->save();

            return $entity->fresh();
        });
    }
}
