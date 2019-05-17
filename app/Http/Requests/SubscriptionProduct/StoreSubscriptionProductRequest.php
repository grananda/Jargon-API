<?php

namespace App\Http\Requests\SubscriptionProduct;

use App\Http\Requests\Request;
use App\Models\Subscriptions\SubscriptionProduct;

class StoreSubscriptionProductRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', SubscriptionProduct::class);
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
            'description' => ['required', 'string'],
            'alias'       => ['required', 'string'],
            'rank'        => ['required', 'numeric'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }
}
