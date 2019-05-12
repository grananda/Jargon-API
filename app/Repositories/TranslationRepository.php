<?php

namespace App\Repositories;

use App\Models\Translations\Node;
use App\Models\Translations\Translation;
use Illuminate\Database\Connection;

/**
 * Class NodeRepository.
 *
 * @package App\Repositories
 */
class TranslationRepository extends CoreRepository
{
    /**
     * Node Repository constructor.
     *
     * @param \Illuminate\Database\Connection $dbConnection
     * @param Translation                     $model
     */
    public function __construct(Connection $dbConnection, Translation $model)
    {
        parent::__construct($dbConnection, $model);
    }
}
