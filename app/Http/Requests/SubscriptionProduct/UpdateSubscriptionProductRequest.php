<?php

namespace App\Http\Requests\SubscriptionProduct;

use App\Http\Requests\Request;
use App\Models\Subscriptions\SubscriptionProduct;

class UpdateSubscriptionProductRequest extends Request
{
    /**
     * @var \App\Models\Subscriptions\SubscriptionProduct
     */
    public $subscriptionProduct;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->subscriptionProduct = SubscriptionProduct::findByUuidOrFail($this->route('id'));

        return $this->user()->can('update', $this->subscriptionProduct);
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
            'description' => ['sometimes', 'string'],
            'rank'        => ['sometimes', 'numeric'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }
}
