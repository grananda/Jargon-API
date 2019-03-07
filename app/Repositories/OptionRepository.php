<?php

namespace App\Repositories;

use App\Models\Options\Option;
use Illuminate\Database\Connection;

/**
 * @property Option model
 */
class OptionRepository extends CoreRepository
{
    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection $dbConnection
     * @param \App\Models\Options\Option      $model
     */
    public function __construct(Connection $dbConnection, Option $model)
    {
        parent::__construct($dbConnection, $model);
    }
}
