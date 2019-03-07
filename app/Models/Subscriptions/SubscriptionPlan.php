<?php

namespace App\Models\Subscriptions;

use App\Models\BaseEntity;
use App\Models\Traits\HasAlias;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends BaseEntity
{
    use HasUuid,
        HasAlias;

    const ITEM_TOKEN_LENGTH         = 50;
    const DEFAULT_SUBSCRIPTION_NAME = 'JARGON';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'title',
        'description',
        'alias',
        'quantity',
        'rank',
    ];

    /**
     * @return HasMany
     */
    public function activeSubscriptions()
    {
        return $this->hasMany(ActiveSubscription::class);
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
}
