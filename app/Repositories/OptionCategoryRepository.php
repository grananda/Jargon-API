<?php

namespace App\Repositories;

use App\Models\Options\Option;
use App\Models\Options\OptionCategory;
use Illuminate\Database\Connection;

/**
 * @property Option model
 */
class OptionCategoryRepository extends CoreRepository
{
    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection    $dbConnection
     * @param \App\Models\Options\OptionCategory $model
     */
    public function __construct(Connection $dbConnection, OptionCategory $model)
    {
        parent::__construct($dbConnection, $model);
    }
}
