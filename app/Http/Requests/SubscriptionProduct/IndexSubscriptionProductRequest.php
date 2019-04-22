<?php

namespace App\Http\Requests\SubscriptionProduct;

use App\Http\Requests\Request;

class IndexSubscriptionProductRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
