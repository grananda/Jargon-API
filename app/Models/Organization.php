<?php

namespace App\Models;

use App\Models\Traits\HasCollaborators;
use App\Models\Traits\HasUuid;
use App\Models\Translations\Project;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends BaseEntity
{
    use HasUuid;
    use
        HasCollaborators;

    const ITEM_TOKEN_LENGTH = 50;

    const ORGANIZATION_OWNER_ROLE_ALIAS      = 'project-admin';
    const ORGANIZATION_MANAGER_ROLE_ALIAS    = 'project-manager';
    const ORGANIZATION_TRANSLATOR_ROLE_ALIAS = 'project-translator';
    const ORGANIZATION_DEFAULT_ROLE_ALIAS    = 'project-user';

    const MEDIA_FILE_LOGO_KEY = 'organization-logo';
    const MEDIA_FILE_LOCATION = 'organization/'.self::MEDIA_FILE_LOGO_KEY;
    const MEDIA_INDEX_COUNT   = 1;

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * @return HasMany
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * @return BelongsToMany
     */
    public function files()
    {
        return $this->belongsToMany(MediaFile::class)
            ->withPivot('organization_id', 'media_file_id')
            ->withTimestamps()
        ;
    }

    /**
     * @param \App\Models\User $user
     */
    public function setOwner(User $user)
    {
        $this->addOwner($user, self::ORGANIZATION_OWNER_ROLE_ALIAS);
    }
}
