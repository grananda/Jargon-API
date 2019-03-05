<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Memo extends BaseEntity
{
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

    /**
     * @return BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return string
     */
    public function getCreatedAtHuman()
    {
        return $this->created_at->diffForHumans();
    }
}
