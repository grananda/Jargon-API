<?php

namespace App\Models\Translations;

use App\Models\BaseEntity;
use App\Models\Traits\HasUuid;

/**
 * @property string access_token
 * @property string username
 * @property string repository
 * @property string  base_branch
 */
class ProjectGitHubConfig extends BaseEntity
{
    use HasUuid;

    protected $fillable = [
        'username',
        'repository',
        'base_branch',
        'access_token',
        'project_id',
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
