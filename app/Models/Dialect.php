<?php

namespace App\Models;

use App\Models\Translations\Project;
use App\Models\Translations\Translation;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property Collection $translations
 * @property Language $language
 */
class Dialect extends BaseEntity
{
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'name',
        'locale',
        'country',
        'country_key',
        'language_id',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class)
            ->withTimestamps()
        ;
    }

    public function translations()
    {
        return $this->hasMany(Translation::class);
    }
}
