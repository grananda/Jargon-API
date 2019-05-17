<?php

namespace App\Http\Requests\SubscriptionPlan;

use App\Http\Requests\Request;
use App\Models\Subscriptions\SubscriptionPlan;

class UpdateSubscriptionPlanRequest extends Request
{
    /**
     * @var SubscriptionPlan
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

        return $this->user()->can('update', $this->subscriptionPlan);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sort_order' => ['sometimes', 'numeric'],
            'is_active'  => ['sometimes', 'boolean'],
            'options'    => ['required', 'array'],
            'options.*'  => ['required'],
        ];
    }
}
