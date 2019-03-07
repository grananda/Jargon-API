<?php

namespace App\Repositories;

use App\Models\Team;
use Illuminate\Database\Connection;

class TeamRepository extends CoreRepository
{
    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection $dbConnection
     * @param \App\Models\Team                $model
     */
    public function __construct(Connection $dbConnection, Team $model)
    {
        parent::__construct($dbConnection, $model);
    }
}
