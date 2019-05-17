<?php

namespace App\Services;

use App\Models\PasswordReset;
use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DatabaseTokenRepositoryFactory
{
    /**
     * The Illuminate Database Connection instance.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $dbConnection;

    /**
     * Generic model instance.
     *
     * @var Model
     */
    protected $model;

    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $app;

    /**
     * DatabaseTokenRepositoryFactory constructor.
     *
     * @param \Illuminate\Database\Connection              $dbConnection
     * @param \App\Models\PasswordReset                    $model
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Connection $dbConnection, PasswordReset $model, Application $app)
    {
        $this->dbConnection = $dbConnection;
        $this->model        = $model;
        $this->app          = $app;
    }

    /**
     * Returns an instance of DatabaseTokenRepository.
     *
     * @return \Illuminate\Auth\Passwords\DatabaseTokenRepository
     */
    public function instance()
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        return new DatabaseTokenRepository(
            $this->dbConnection,
            $this->app['hash'],
            $this->model->getTable(),
            $key,
            PasswordReset::TOKEN_EXPIRATION_PERIOD
        );
    }
}
