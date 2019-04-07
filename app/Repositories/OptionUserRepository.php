<?php

namespace App\Repositories;

use App\Models\Options\OptionUser;
use App\Models\User;
use Illuminate\Database\Connection;

class OptionUserRepository extends CoreRepository
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
     * @param \App\Models\Options\OptionUser     $model
     * @param \App\Repositories\OptionRepository $optionRepository
     */
    public function __construct(Connection $dbConnection, OptionUser $model, OptionRepository $optionRepository)
    {
        parent::__construct($dbConnection, $model);

        $this->optionsRepository = $optionRepository;
    }

    /**
     * Rebuild all OptionUser for a given user.
     *
     * @param \App\Models\User $user
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function rebuildUserOptions(User $user)
    {
        return $this->dbConnection->transaction(function () use ($user) {
            $this->removeUserOptions($user);

            return $this->createUserOptions($user);
        });
    }

    /**
     * @param \App\Models\User $user
     * @param array            $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function createUserOption(User $user, array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($user, $attributes) {
            $option = $this->create($attributes);
            $user->options()->save($option);

            return $option;
        });
    }

    /**
     * Add UserOptions to user current options pool.
     *
     * @param \App\Models\User $user
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function createUserOptions(User $user)
    {
        return $this->dbConnection->transaction(function () use ($user) {
            /** @var \Illuminate\Database\Eloquent\Collection $options */
            $options = $this->optionsRepository->findAllBy([
                'option_scope' => 'user',
            ]);

            /** @var \Illuminate\Database\Eloquent\Collection $options */
            $userOptions = $this->findAllBy(['user_id' => $user->id]);

            /** @var array $userOptionKeys */
            $userOptionKeys = array_column($userOptions->toArray(), 'option_key');

            foreach ($options as $option) {
                if (! in_array($option->option_key, $userOptionKeys)) {
                    $this->create([
                        'user_id'      => $user->id,
                        'option_key'   => $option->option_key,
                        'option_value' => $option->option_value,
                    ]);
                }
            }

            return $userOptions->fresh();
        });
    }

    /**
     * Removes all UserOptions for current user.
     *
     * @param \App\Models\User $user
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function removeUserOptions(User $user)
    {
        return $this->dbConnection->transaction(function () use ($user) {
            /** @var \Illuminate\Database\Eloquent\Collection $options */
            $userOptions = $this->findAllBy(['user_id' => $user->id]);

            /** @var array $userOptionKeys */
            $userOptionKeys = array_column($userOptions->toArray(), 'option_key');

            foreach ($userOptions as $option) {
                if (in_array($option->option_key, $userOptionKeys)) {
                    $this->delete($option);
                }
            }

            return $userOptions->fresh();
        });
    }

    /**
     * Updates all options for a given user.
     *
     * @param \App\Models\User $user
     * @param array            $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function updateUserOptions(User $user, array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($user, $attributes) {
            /** @var \Illuminate\Database\Eloquent\Collection $userOptions */
            $userOptions = $this->findAllBy(['user_id' => $user->id]);

            /* @var \App\Models\Options\OptionUser $option */
            foreach ($userOptions as $userOption) {
                $value = $attributes[$userOption->option_key] ?? 0;
                $this->update($userOption, ['option_key' => $userOption->option_key, 'option_value' => $value]);
            }

            return $userOptions->fresh();
        });
    }
}
