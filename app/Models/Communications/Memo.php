<?php

namespace App\Models\Communications;

use App\Models\BaseEntity;
use App\Models\Traits\HasUuid;
use App\Models\User;

/**
 * @property \Illuminate\Support\Collection recipients
 * @property string status
 */
class Memo extends BaseEntity
{
    use HasUuid;

    const ITEM_TOKEN_LENGTH    = 50;
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

    public static function boot()
    {
        parent::boot();

        static::deleting(function (self $model) {
            $model->recipients()->detach();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function recipients()
    {
        return $this->belongsToMany(User::class)
            ->withPivot([
                'user_id',
                'created_at',
                'updated_at',
                'is_read',
            ])
            ->orderBy('updated_at', 'desc')
        ;
    }

    /**
     * @param array $recipients
     *
     * @return \App\Models\Communications\Memo
     */
    public function setRecipients(array $recipients)
    {
        $this->recipients()->sync($recipients);

        return $this->refresh();
    }

    /**
     * @param \App\Models\User $user
     * @param bool             $isRead
     *
     * @return int
     */
    public function setIsRead(User $user, bool $isRead)
    {
        return $this->recipients()->updateExistingPivot($user, ['is_read' => $isRead]);
    }
}
