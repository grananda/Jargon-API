<?php

namespace App\Repositories;

use App\Events\User\PasswordResetRequested;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetRepository extends CoreRepository
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $app;

    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection $dbConnection
     * @param \App\Models\PasswordReset       $model
     */
    public function __construct(Connection $dbConnection, PasswordReset $model, Application $app)
    {
        parent::__construct($dbConnection, $model);

        $this->app = $app;
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
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        /** @var DatabaseTokenRepository $databaseTokenRepository */
        $databaseTokenRepository = new DatabaseTokenRepository(
            $this->dbConnection,
            $this->app['hash'],
            $this->model->getTable(),
            $key,
            PasswordReset::TOKEN_EXPIRATION_PERIOD
        );

        $token = $databaseTokenRepository->create($user);

        /** @var \App\Models\PasswordReset $entity */
        $entity = $this->findBy(['email' => $user->email]);

        Event::dispatch(new PasswordResetRequested($user, $token));

        return $token;
    }
}
