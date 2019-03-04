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

    public function create(array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($attributes) {
            $attributes['password'] = bcrypt($attributes['password']);
            $entity = $this->getModel($attributes);
            $entity->save();

            return $entity;
        });
    }

    public function login(User $user)
    {
        $token = $user->generateOAuthToken(self::AUTH_CLIENT_ID);

        $user->save(['last_login' => Carbon::now()]);

        return $token;
    }
}
