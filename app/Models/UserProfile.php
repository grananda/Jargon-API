<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserProfile extends BaseEntity
{
    const MEDIA_FILE_HEADER_KEY = 'profile-header';
    const MEDIA_FILE_LOCATION   = 'profile/'.self::MEDIA_FILE_HEADER_KEY;
    const MEDIA_INDEX_COUNT     = 1;

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'username',
        'city',
        'country',
        'company',
        'occupation',
        'biography',
        'web_url',
        'social_twitter',
        'social_facebook',
        'social_git',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany
     */
    public function files()
    {
        return $this->belongsToMany(MediaFile::class)
            ->withPivot('user_profile_id', 'media_file_id')
            ->withTimestamps()
        ;
    }
}
