<?php

namespace App\Models\Subscriptions;

use App\Events\SubscriptionPlan\SubscriptionPlanWasCreated;
use App\Events\SubscriptionPlan\SubscriptionPlanWasDeleted;
use App\Events\SubscriptionPlan\SubscriptionPlanWasUpdated;
use App\Models\BaseEntity;
use App\Models\Currency;
use App\Models\Traits\HasAlias;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends BaseEntity
{
    use HasUuid,
        HasAlias;

    const DEFAULT_SUBSCRIPTION_PLAN  = 'freemium-month-euro';
    const STANDARD_STRIPE_TYPE_LABEL = 'service';
    const STANDARD_STRIPE_INTERVAL   = 'month';
    const STANDARD_STRIPE_USAGE_TYPE = 'licensed';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'alias',
        'sort_order',
        'amount',
        'interval',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * {@inheritdoc}
     */
    protected $dispatchesEvents = [
        'created' => SubscriptionPlanWasCreated::class,
        'updated' => SubscriptionPlanWasUpdated::class,
        'deleted' => SubscriptionPlanWasDeleted::class,
    ];

    /** {@inheritdoc} */
    public static function boot()
    {
        parent::boot();

        static::deleted(function (self $model) {
            $model->options()->delete();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(SubscriptionProduct::class);
    }

    /**
     * @return HasMany
     */
    public function activeSubscriptions()
    {
        return $this->hasMany(ActiveSubscription::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function currency()
    {
        return $this->hasOne(Currency::class);
    }

    /**
     * @return HasMany
     */
    public function options()
    {
        return $this->hasMany(SubscriptionPlanOptionValue::class);
    }

    public function getAmount()
    {
        return money_format('%.2n', $this->amount).'€';
    }

    /**
     * @param \App\Models\Subscriptions\SubscriptionPlanOptionValue $optionValue
     *
     * @return \App\Models\Subscriptions\SubscriptionPlan|null
     */
    public function addOption(SubscriptionPlanOptionValue $optionValue)
    {
        $this->options()->save($optionValue);

        return $this->fresh();
    }
}
