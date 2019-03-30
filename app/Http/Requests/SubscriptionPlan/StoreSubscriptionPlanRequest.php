<?php

namespace App\Http\Requests\SubscriptionPlan;

use App\Http\Requests\Request;
use App\Models\Subscriptions\SubscriptionPlan;

class StoreSubscriptionPlanRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', SubscriptionPlan::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'       => ['required', 'string'],
            'description' => ['required', 'string', 'max:255'],
            'alias'       => ['required', 'string'],
            'amount'      => ['required', 'numeric'],
            'status'      => ['sometimes', 'boolean'],
            'options'     => ['required', 'array'],
            'options.*'   => ['required'],
        ];
    }
}
