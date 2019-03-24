<?php

namespace App\Http\Requests\SubscriptionPlan;

use App\Http\Requests\Request;
use App\Models\Subscriptions\SubscriptionPlan;

class ShowSubscriptionPlanRequest extends Request
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
        $this->subscriptionPlan = SubscriptionPlan::findByUuidOrFail($this->route('id'));

        return true;
    }
}
