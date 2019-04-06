<?php

namespace App\Repositories;

use App\Events\User\PasswordResetRequested;
use App\Models\PasswordReset;
use App\Models\User;
use App\Services\DatabaseTokenRepositoryFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Event;

class PasswordResetRepository extends CoreRepository
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $app;

    /**
     * @var \App\Services\DatabaseTokenRepositoryFactory
     */
    private $databaseTokenRepositoryFactory;

    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection              $dbConnection
     * @param \App\Models\PasswordReset                    $model
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \App\Services\DatabaseTokenRepositoryFactory $databaseTokenRepositoryFactory
     */
    public function __construct(Connection $dbConnection, PasswordReset $model, Application $app, DatabaseTokenRepositoryFactory $databaseTokenRepositoryFactory)
    {
        parent::__construct($dbConnection, $model);

        $this->app = $app;

        $this->databaseTokenRepositoryFactory = $databaseTokenRepositoryFactory;
    }

    /**
     * Generates a password recovery token.
     *
     * @param \App\Models\User $user
     *
     * @return string
     */
    public function createPasswordReset(User $user)
    {
        $token = $this->databaseTokenRepositoryFactory->instance()->create($user);

        Event::dispatch(new PasswordResetRequested($user, $token));

        return $token;
    }
}
