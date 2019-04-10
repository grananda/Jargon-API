<?php

namespace App\Http\Requests\SubscriptionProduct;

use App\Models\Subscriptions\SubscriptionProduct;
use Illuminate\Foundation\Http\FormRequest;

class DeleteSubscriptionProductRequest extends FormRequest
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

        return $this->user()->can('delete', $this->subscriptionProduct);
    }
}
