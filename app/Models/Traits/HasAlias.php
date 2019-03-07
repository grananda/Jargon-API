<?php

namespace App\Models\Traits;

trait HasAlias
{
    /**
     * Finds a model by alias.
     *
     * @param string $alias
     * @param array  $columns
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function findByAliasOrFail(string $alias, array $columns = ['*'])
    {
        return static::where('alias', '=', $alias)->firstOrFail($columns);
    }
}
