<?php

namespace App\Repositories;

use App\Models\Dialect;
use Illuminate\Database\Connection;

class DialectRepository extends CoreRepository
{
    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection $dbConnection
     * @param \App\Models\Dialect             $model
     */
    public function __construct(Connection $dbConnection, Dialect $model)
    {
        parent::__construct($dbConnection, $model);
    }
}
