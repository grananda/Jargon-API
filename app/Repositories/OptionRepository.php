<?php

namespace App\Repositories;

use App\Models\Options\Option;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;

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

    public function updateOption(Option $option, array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($option, $attributes) {
            $forbidden = [
                'option_key',
                'option_type',
                'option_enum',
                'option_scope',
            ];

            foreach ($forbidden as $item) {
                unset($attributes[$item]);
            }

            $option = $this->update($option, $attributes);

            return $option->fresh();
        });
    }
}
