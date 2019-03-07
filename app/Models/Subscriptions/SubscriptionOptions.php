<?php

namespace App\Models\Subscriptions;

use App\Models\BaseEntity;

class SubscriptionOptions extends BaseEntity
{
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'title',
        'description',
        'option_key',
    ];
}
