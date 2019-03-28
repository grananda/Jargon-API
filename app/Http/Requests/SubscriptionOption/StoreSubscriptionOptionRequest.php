<?php

namespace App\Http\Requests\SubscriptionOption;

use App\Http\Requests\Request;
use App\Models\Subscriptions\SubscriptionOption;

class StoreSubscriptionOptionRequest extends Request
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
        return $this->user()->can('create', SubscriptionOption::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
