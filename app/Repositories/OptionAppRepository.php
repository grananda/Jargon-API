<?php

namespace App\Repositories;

use App\Models\Options\OptionApp;
use App\Models\User;
use Illuminate\Database\Connection;

class OptionAppRepository extends CoreRepository
{
    /**
     * The OptionsRespository instance.
     *
     * @var \App\Repositories\OptionRepository
     */
    private $optionsRepository;

    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection    $dbConnection
     * @param \App\Models\Options\OptionApp      $model
     * @param \App\Repositories\OptionRepository $optionRepository
     */
    public function __construct(Connection $dbConnection, OptionApp $model, OptionRepository $optionRepository)
    {
        parent::__construct($dbConnection, $model);

        $this->optionsRepository = $optionRepository;
    }

    /**
     * Rebuild all OptionApp.
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function rebuildAppOptions()
    {
        return $this->dbConnection->transaction(function () {
            $this->removeUserOptions();

            return $this->createUserOptions();
        });
    }

    /**
     * Add UserOptions to user current options pool.
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function createUserOptions()
    {
        return $this->dbConnection->transaction(function () {
            /** @var \Illuminate\Database\Eloquent\Collection $options */
            $options = $this->optionsRepository->findAllBy([
                'option_scope' => 'staff',
            ]);

            /** @var \Illuminate\Database\Eloquent\Collection $options */
            $appOptions = $this->findAllBy([]);

            /** @var array $userOptionKeys */
            $appOptionKeys = array_column($appOptions->toArray(), 'option_key');

            foreach ($options as $option) {
                if (! in_array($option->option_key, $appOptionKeys)) {
                    $this->create([
                        'option_key'   => $option->option_key,
                        'option_value' => $option->option_value,
                    ]);
                }
            }

            return $appOptions->fresh();
        });
    }

    /**
     * Removes all UserOptions for current user.
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function removeUserOptions()
    {
        return $this->dbConnection->transaction(function () {
            /** @var \Illuminate\Database\Eloquent\Collection $options */
            $appOptions = $this->findAllBy([]);

            /** @var array $userOptionKeys */
            $appOptionKeys = array_column($appOptions->toArray(), 'option_key');

            foreach ($appOptions as $option) {
                if (in_array($option->option_key, $appOptionKeys)) {
                    $this->delete($option);
                }
            }

            return $appOptions->fresh();
        });
    }

    /**
     * Updates all options for a given user.
     *
     * @param array $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function updateUserOptions(array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($attributes) {
            /** @var \Illuminate\Database\Eloquent\Collection $appOptions */
            $appOptions = $this->findAllBy([]);

            /* @var \App\Models\Options\OptionUser $option */
            foreach ($appOptions as $userOption) {
                $value = $attributes[$userOption->option_key] ?? 0;
                $this->update($userOption, ['option_key' => $userOption->option_key, 'option_value' => $value]);
            }

            return $appOptions->fresh();
        });
    }
}
