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
            'title'       => ['sometimes', 'string'],
            'description' => ['sometimes', 'string', 'max:255'],
            'alias'       => ['sometimes', 'string'],
            'level'       => ['sometimes', 'numeric'],
            'amount'      => ['sometimes', 'numeric'],
            'status'      => ['sometimes', 'boolean'],
            'options'     => ['sometimes', 'array'],
            'options.*'   => ['required'],
        ];
    }
}
