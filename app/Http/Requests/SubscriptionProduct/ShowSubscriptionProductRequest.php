<?php

namespace App\Http\Requests\SubscriptionProduct;

use App\Http\Requests\Request;
use App\Models\Subscriptions\SubscriptionProduct;

class ShowSubscriptionProductRequest extends Request
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

        return true;
    }
}
