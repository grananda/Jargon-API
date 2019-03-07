<?php

namespace App\Models\Subscriptions;

use App\Models\BaseEntity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActiveSubscription extends BaseEntity
{
    protected $dates = [
        'created_at',
        'updated_at',
        'end_at',
    ];

    protected $fillable = [
        'subscription_plan_id',
        'subscription_active',
        'ends_at',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * @return HasMany
     */
    public function options()
    {
        return $this->hasMany(ActiveSubscriptionOptionValue::class);
    }

    /**
     * @return string|null
     */
    public function isSubscriptionActive()
    {
        return $this->subscription_active;
    }

    /**
     * @return string|null
     */
    public function getEndsAt()
    {
        return $this->ends_at ? Carbon::parse($this->ends_at)->format('d/m/Y') : null;
    }

    /**
     * @return string|null
     */
    public function getEndsAtForHumans()
    {
        return $this->ends_at ? Carbon::parse($this->ends_at)->diffForHumans() : null;
    }

    /**
     * @return Carbon|bool
     */
    public function onGracePeriod()
    {
        return $this->ends_at ? Carbon::parse($this->ends_at)->greaterThanOrEqualTo(Carbon::now()) : false;
    }
}
