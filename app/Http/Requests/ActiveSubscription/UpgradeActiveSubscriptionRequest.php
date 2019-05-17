<?php

namespace App\Http\Requests\ActiveSubscription;

use App\Http\Requests\Request;
use App\Models\Subscriptions\SubscriptionPlan;

class UpgradeActiveSubscriptionRequest extends Request
{
    /**
     * @var \App\Models\Subscriptions\SubscriptionPlan
     */
    public $subscriptionPlan;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->subscriptionPlan = SubscriptionPlan::findByUuidOrFail($this->input('id'));

        return $this->user()->can('upgrade', $this->subscriptionPlan);
    }
}
