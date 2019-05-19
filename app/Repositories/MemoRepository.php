<?php

namespace App\Repositories;

use App\Models\Communications\Memo;
use Illuminate\Database\Connection;

class MemoRepository extends CoreRepository
{
    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection $dbConnection
     * @param \App\Models\Communications\Memo $model
     */
    public function __construct(Connection $dbConnection, Memo $model)
    {
        parent::__construct($dbConnection, $model);
    }
}
