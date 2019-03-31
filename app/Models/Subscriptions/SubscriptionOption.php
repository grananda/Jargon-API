<?php

namespace App\Models\Subscriptions;

use App\Events\SubscriptionOption\SubscriptionOptionWasCreated;
use App\Events\SubscriptionOption\SubscriptionOptionWasDeleted;
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

    /**
     * {@inheritdoc}
     */
    protected $dispatchesEvents = [
        'created' => SubscriptionOptionWasCreated::class,
        'deleted' => SubscriptionOptionWasDeleted::class,
    ];
}
