<?php

namespace App\Models\Subscriptions;

use App\Models\BaseEntity;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SubscriptionPlanOptionValue extends BaseEntity
{
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'option_key',
        'option_value',
    ];

    /**
     * @return HasOne
     */
    public function key()
    {
        return $this->hasOne(SubscriptionOption::class, 'option_key', 'option_key');
    }
}
