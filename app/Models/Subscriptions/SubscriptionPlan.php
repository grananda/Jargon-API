<?php

namespace App\Models\Subscriptions;

use App\Events\SubscriptionPlan\SubscriptionPlanWasDeleted;
use App\Models\BaseEntity;
use App\Models\Currency;
use App\Models\Traits\HasAlias;
use App\Models\Traits\HasUuid;
use App\Models\Traits\OptionQuota;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends BaseEntity
{
    use HasUuid;
    use
        HasAlias;
    use
        OptionQuota;

    const DEFAULT_SUBSCRIPTION_PLAN  = 'freemium-month-eur';
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
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * {@inheritdoc}
     */
    protected $dispatchesEvents = [
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
        return $this->belongsTo(SubscriptionProduct::class, 'subscription_product_id');
    }

    /**
     * @return HasMany
     */
    public function activeSubscriptions()
    {
        return $this->hasMany(ActiveSubscription::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
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
