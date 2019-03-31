<?php

namespace App\Models\Subscriptions;

use App\Models\BaseEntity;
use App\Models\Traits\HasUuid;

class SubscriptionOption extends BaseEntity
{
    use HasUuid;

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'title',
        'description',
        'description_template',
        'option_key',
    ];
}
