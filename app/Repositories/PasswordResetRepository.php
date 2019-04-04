<?php

namespace App\Repositories;

use App\Events\User\PasswordResetRequested;
use App\Models\PasswordReset;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Event;

class PasswordResetRepository extends CoreRepository
{
    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection $dbConnection
     * @param \App\Models\PasswordReset       $model
     */
    public function __construct(Connection $dbConnection, PasswordReset $model)
    {
        parent::__construct($dbConnection, $model);
    }

    /**
     * @param array $attributes
     *
     * @throws \Throwable
     *
     * @return \App\Models\PasswordReset
     */
    public function createPasswordReset(array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($attributes) {
            $token = $this->model->generateToken();

            $this->dbConnection
                ->table($this->model->getTable())
                ->insert([
                    'email'      => $attributes['email'],
                    'token'      => $token,
                    'created_at' => now(),
                ])
            ;

            /** @var \App\Models\PasswordReset $entity */
            $entity = $this->findBy(['token' => $token]);

            Event::dispatch(new PasswordResetRequested($entity));

            return $entity;
        });
    }
}
