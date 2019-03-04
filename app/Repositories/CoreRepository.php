<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseRepository.
 *
 * @package App\Repositories
 */
abstract class CoreRepository
{
    /**
     * The Illuminate Database Connection instance.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $dbConnection;

    /**
     * Generic model instance.
     *
     * @var Model
     */
    protected $model;

    /**
     * CoreRepository constructor.
     *
     * @param \Illuminate\Database\Connection     $dbConnection
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct(Connection $dbConnection, Model $model)
    {
        $this->dbConnection = $dbConnection;

        $this->model = $model;
    }

    /**
     * Find single record by a set of values.
     *
     * @param array $values
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findBy(array $values)
    {
        $query = $this->getQuery();

        foreach ($values as $column => $value) {
            $query->where($column, '=', $value);
        }

        return $query->first();
    }

    /**
     * Find single record by a set of values or fail.
     *
     * @param array $values
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findByOrFail(array $values)
    {
        $query = $this->getQuery();

        foreach ($values as $column => $value) {
            $query->where($column, '=', $value);
        }

        return $query->firstOrFail();
    }

    /**
     * Find all records by a set of values.
     *
     * @param array $values
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllBy(array $values)
    {
        $query = $this->getQuery();

        foreach ($values as $column => $value) {
            $query->where($column, '=', $value);
        }

        return $query->get();
    }

    /**
     * Find model by id.
     *
     * @param string $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findById(string $id)
    {
        return $this->findBy(['id' => $id]);
    }

    /**
     * Find model by id or fail.
     *
     * @param string $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findByIdOrFail(string $id)
    {
        return $this->findByOrFail(['id' => $id]);
    }

    /**
     * Find model by uuid.
     *
     * @param string $uuid
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findByUuId(string $uuid)
    {
        return $this->findBy(['uuid' => $uuid]);
    }

    /**
     * Find model by uuid or fail.
     *
     * @param string $uuid
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findByUuIdOrFail(string $uuid)
    {
        return $this->findByOrFail(['uuid' => $uuid]);
    }

    /**
     * Find a model using its name.
     *
     * @param string $name
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findByName(string $name)
    {
        return $this->findBy(['name' => $name]);
    }

    /**
     * Find a model using its name or fail.
     *
     * @param string $name
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findByNameOrFail(string $name)
    {
        return $this->findByOrFail(['name' => $name]);
    }

    /**
     * Gets all items owned by user.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllByOwner(User $user)
    {
        return $this->getQuery()
            ->whereHas('collaborators', function ($query) use ($user) {
                /* @var \Illuminate\Database\Query\Builder $query */
                $query->where('collaborators.is_owner', true);
                $query->where('collaborators.user_id', $user->id);
            })
            ->orderByDesc('id')
            ->get()
        ;
    }

    /**
     * Gets all items where user is member.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllByMember(User $user)
    {
        return $this->getQuery()
            ->whereHas('collaborators', function ($query) use ($user) {
                /* @var \Illuminate\Database\Query\Builder $query */
                $query->where('collaborators.user_id', $user->id);
            })
            ->orderByDesc('id')
            ->get()
        ;
    }

    /**
     * Creates new model.
     *
     * @param array $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function create(array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($attributes) {
            $entity = $this->getModel($attributes);
            $entity->save();

            return $entity;
        });
    }

    /**
     * @param \App\Models\User $user
     * @param string           $role
     * @param array            $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function createWithOwner(User $user, string $role, array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($user, $role, $attributes) {
            /** @var \App\Models\Project $entity */
            $entity = $this->create($attributes);

            $entity->addOwner($user, $role);

            return $entity->fresh();
        });
    }

    /**
     * Updates model.
     *
     * @param \Illuminate\Database\Eloquent\Model $entity
     * @param array                               $attributes
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(Model $entity, array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($entity, $attributes) {
            $entity->fill($attributes);

            $entity->save();

            return $entity->fresh();
        });
    }

    /**
     * Deletes entity.
     *
     * @param \Illuminate\Database\Eloquent\Model $entity
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function delete(Model $entity)
    {
        return $this->dbConnection->transaction(function () use ($entity) {
            $entity->delete();

            return $entity;
        });
    }

    /**
     * Gets current model.
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel(array $attributes = [])
    {
        return $this->model->newInstance($attributes);
    }

    /**
     * Gets query object.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getQuery()
    {
        return $this->getModel()->query();
    }
}
