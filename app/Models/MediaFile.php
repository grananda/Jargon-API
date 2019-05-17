<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MediaFile extends BaseEntity
{
    const ITEM_TOKEN_LENGTH = 50;

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'path',
        'mime_type',
        'media_key',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'item_token',
    ];

    /**
     * @return BelongsToMany
     */
    public function organizations()
    {
        return $this->belongsToMany(Organization::class)
            ->withPivot('organization_id', 'media_file_id')
            ->withTimestamps()
        ;
    }

    /**
     * @return BelongsToMany
     */
    public function profiles()
    {
        return $this->belongsToMany(UserProfile::class)
            ->withPivot('user_profile_id', 'media_file_id')
            ->withTimestamps()
        ;
    }
}
