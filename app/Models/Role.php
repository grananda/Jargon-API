<?php

namespace App\Models;

use App\Models\Traits\HasAlias;

class Role extends BaseEntity
{
    use HasAlias;

    const ROLE_USER_TYPE  = 'user';
    const ROLE_STAFF_TYPE = 'staff';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'alias',
        'permissions',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps()
        ;
    }
}
