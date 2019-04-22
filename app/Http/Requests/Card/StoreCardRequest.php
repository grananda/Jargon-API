<?php

namespace App\Http\Requests\Card;

use App\Http\Requests\Request;
use App\Models\Card;

class StoreCardRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Card::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'stripeCardToken' => ['required', 'string'],
            'address_city'    => ['sometimes', 'string'],
            'address_country' => ['sometimes', 'string'],
            'address_line1'   => ['sometimes', 'string'],
            'address_line2'   => ['sometimes', 'string'],
            'address_state'   => ['sometimes', 'string'],
            'address_zip'     => ['sometimes', 'string'],
        ];
    }
}
