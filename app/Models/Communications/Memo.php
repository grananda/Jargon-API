<?php

namespace App\Models\Communications;

use App\Models\BaseEntity;
use App\Models\Traits\HasUuid;
use App\Models\User;

/**
 * @property \Illuminate\Support\Collection recipients
 */
class Memo extends BaseEntity
{
    use HasUuid;

    const ITEM_TOKEN_LENGTH = 50;
    const EXPIRATION_THRESHOLD = 15;

    protected $created_at_human;

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'subject',
        'body',
        'status',
        'item_token',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function recipients()
    {
        return $this->belongsToMany(User::class)
            ->withPivot([
                'created_at',
                'updated_at',
                'is_read',
            ])
            ->orderBy('updated_at', 'desc')
            ;
    }

    /**
     * @param array $users
     */
    public function setRecipients(array $users)
    {
        /** @var array $userCollection */
        $userCollection = [];

        foreach ($users as $user) {
            $userCollection[] = User::findByUuidOrFail($user);
        }

        $this->recipients()->sync(collect($userCollection)->pluck('id')->toArray());
    }
}
