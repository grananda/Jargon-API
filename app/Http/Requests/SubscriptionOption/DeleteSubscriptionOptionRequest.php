<?php

namespace App\Http\Requests\SubscriptionOption;

use App\Http\Requests\Request;
use App\Models\Subscriptions\SubscriptionOption;

class DeleteSubscriptionOptionRequest extends Request
{
    /**
     * @var \App\Models\Subscriptions\SubscriptionOption
     */
    public $subscriptionOption;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->subscriptionOption = SubscriptionOption::findByUuidOrFail($this->route('id'));

        return $this->user()->can('delete', $this->subscriptionOption);
    }
}
