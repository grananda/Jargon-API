<?php

namespace App\Models;

class Language extends BaseEntity
{
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'name',
        'lang_key',
    ];

    public function dialects()
    {
        return $this->hasMany(Dialect::class, 'language_id', 'id');
    }

    public function __toString()
    {
        return $this->name;
    }
}
