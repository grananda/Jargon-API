<?php

namespace App\Models\Subscriptions;

use App\Events\SubscriptionProduct\SubscriptionProductWasCreated;
use App\Events\SubscriptionProduct\SubscriptionProductWasDeleted;
use App\Events\SubscriptionProduct\SubscriptionProductWasUpdated;
use App\Models\BaseEntity;
use App\Models\Traits\HasUuid;

class SubscriptionProduct extends BaseEntity
{
    use HasUuid;

    const STANDARD_STRIPE_TYPE_LABEL = 'service';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'title',
        'description',
        'alias',
        'rank',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $dispatchesEvents = [
        'created' => SubscriptionProductWasCreated::class,
        'updated' => SubscriptionProductWasUpdated::class,
        'deleted' => SubscriptionProductWasDeleted::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plans()
    {
        return $this->hasMany(SubscriptionPlan::class);
    }
}
