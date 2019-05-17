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
     * @var \App\Repositories\OptionCategoryRepository
     */
    private $optionCategoryRepository;

    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection            $dbConnection
     * @param \App\Models\Options\Option                 $model
     * @param \App\Repositories\OptionCategoryRepository $optionCategoryRepository
     */
    public function __construct(Connection $dbConnection, Option $model, OptionCategoryRepository $optionCategoryRepository)
    {
        parent::__construct($dbConnection, $model);

        $this->optionCategoryRepository = $optionCategoryRepository;
    }

    public function createOption(array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($attributes) {
            $attributes['option_category_id'] = $this->optionCategoryRepository->findByUuIdOrFail($attributes['option_category_id'])->id;

            return $this->create($attributes);
        });
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

            if (isset($attributes['option_category_id'])) {
                $attributes['option_category_id'] = $this->optionCategoryRepository->findByUuIdOrFail($attributes['option_category_id'])->id;
            }

            $option = $this->update($option, $attributes);

            return $option->fresh();
        });
    }
}
