<?php

namespace App\Models\Subscriptions;

use App\Events\ActiveSubscription\ActiveSubscriptionWasActivated;
use App\Events\ActiveSubscription\ActiveSubscriptionWasDeactivated;
use App\Models\BaseEntity;
use App\Models\Traits\OptionQuota;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActiveSubscription extends BaseEntity
{
    use OptionQuota;

    protected $dates = [
        'created_at',
        'updated_at',
        'end_at',
    ];

    protected $fillable = [
        'subscription_plan_id',
        'subscription_active',
        'ends_at',
        'stripe_id',
    ];

    protected $casts = [
        'subscription_active' => 'boolean',
    ];

    /**
     * {@inheritdoc}
     */
    protected $dispatchesEvents = [
        'activated'   => ActiveSubscriptionWasActivated::class,
        'deactivated' => ActiveSubscriptionWasDeactivated::class,
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

    /**
     * Activates an subscription.
     *
     * @return \App\Models\Subscriptions\ActiveSubscription|null
     */
    public function activate()
    {
        $this->subscription_active = true;

        $this->ends_at = null;

        $this->save();

        $this->fireModelEvent('activated');

        return $this->fresh();
    }

    /**
     * Deactivates an subscription.
     *
     * @param string|null $cancelAt
     *
     * @return \App\Models\Subscriptions\ActiveSubscription
     */
    public function deactivate(string $cancelAt = null)
    {
        $cancelAt = $cancelAt ? Carbon::createFromTimestamp($cancelAt) : now();

        $this->subscription_active = false;

        $this->ends_at = $cancelAt;

        $this->save();

        $this->fireModelEvent('deactivated');

        return $this->fresh();
    }
}
