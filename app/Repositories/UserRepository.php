<?php

namespace App\Repositories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Connection;

class UserRepository extends CoreRepository
{
    const AUTH_CLIENT_ID = 1;

    public function __construct(Connection $dbConnection, User $model)
    {
        parent::__construct($dbConnection, $model);
    }

    /** {@inheritdoc} */
    public function createUser(array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($attributes) {
            $attributes['password'] = bcrypt($attributes['password']);
            $entity = $this->getModel($attributes);
            $entity->save();

            return $entity;
        });
    }

    /**
     * @param \App\Models\User $entity
     * @param array            $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function updateUser(User $entity, array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($entity, $attributes) {
            if (isset($attributes['password'])) {
                $attributes['password'] = bcrypt($attributes['password']);
            }

            $entity->fill($attributes);

            $entity->save();

            return $entity->fresh();
        });
    }

    public function login(User $user)
    {
        $token = $user->generateOAuthToken(self::AUTH_CLIENT_ID);

        $user->save(['last_login' => Carbon::now()]);

        return $token;
    }
}
