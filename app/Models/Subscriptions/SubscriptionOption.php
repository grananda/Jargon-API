<?php

namespace App\Models\Subscriptions;

use App\Events\SubscriptionOption\SubscriptionOptionWasCreated;
use App\Events\SubscriptionOption\SubscriptionOptionWasDeleted;
use App\Events\SubscriptionOption\SubscriptionOptionWasUpdated;
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
        'updated' => SubscriptionOptionWasUpdated::class,
        'deleted' => SubscriptionOptionWasDeleted::class,
    ];
}
