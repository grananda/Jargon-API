<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Ramsey\Uuid\Uuid as RamseyUuid;

trait HasUuid
{
    /**
     * Boot the Uuid trait for the model.
     *
     * @return void
     */
    public static function bootHasUuid()
    {
        static::creating(function ($model) {
            $model->uuid = RamseyUuid::uuid4()->toString();
        });
    }

    /**
     * Finds an entity by its UUID value.
     *
     * @param string $uuid
     * @param array  $columns
     *
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public static function findByUuid(string $uuid, array $columns = ['*'])
    {
        return static::where('uuid', '=', $uuid)->first($columns);
    }

    /**
     * Finds an entity by its UUID value or throw an exception.
     *
     * @param string $uuid
     * @param array  $columns
     *
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public static function findByUuidOrFail(string $uuid, array $columns = ['*'])
    {
        return static::where('uuid', '=', $uuid)->firstOrFail($columns);
    }

    /**
     * Finds the entity with the given UUID value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $uuid
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereUuid(Builder $query, string $uuid)
    {
        return $query->where('uuid', '=', $uuid);
    }

    /**
     * Finds entities with the given UUIDs values.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array                                 $uuids
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereInUuid(Builder $query, array $uuids)
    {
        return $query->whereIn('uuid', $uuids);
    }
}
