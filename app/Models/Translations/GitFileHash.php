<?php

namespace App\Models\Translations;

use App\Models\BaseEntity;

/**
 * @property string pull_request_number
 */
class GitFileHash extends BaseEntity
{
    protected $fillable = [
        'locale',
        'file',
        'hash',
        'pull_request_number',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Project to apply config to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
