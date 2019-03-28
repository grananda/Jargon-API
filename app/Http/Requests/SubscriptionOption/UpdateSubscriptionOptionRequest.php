<?php

namespace App\Http\Requests\SubscriptionOption;

use App\Models\Subscriptions\SubscriptionOption;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionOptionRequest extends FormRequest
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

        return $this->user()->can('update', $this->subscriptionOption);
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
