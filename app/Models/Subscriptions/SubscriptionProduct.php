<?php

namespace App\Models\Subscriptions;

use App\Events\SubscriptionProduct\SubscriptionProductWasCreated;
use App\Events\SubscriptionProduct\SubscriptionProductWasDeleted;
use App\Models\BaseEntity;
use App\Models\Traits\HasUuid;

class SubscriptionProduct extends BaseEntity
{
    use HasUuid;

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
        'deleted' => SubscriptionProductWasDeleted::class,
    ];

    /** {@inheritdoc} */
    public static function boot()
    {
        parent::boot();

        static::deleted(function (self $model) {
            $model->plans()->delete();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plans()
    {
        return $this->hasMany(SubscriptionPlan::class);
    }
}
