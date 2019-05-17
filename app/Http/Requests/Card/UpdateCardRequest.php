<?php

namespace App\Http\Requests\Card;

use App\Http\Requests\Request;
use App\Models\Card;

class UpdateCardRequest extends Request
{
    /**
     * @var \App\Models\Card
     */
    public $card;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->card = Card::findByUuidOrFail($this->route('id'));

        return $this->user()->can('update', $this->card);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address_city'    => ['sometimes', 'string'],
            'address_country' => ['sometimes', 'string'],
            'address_line1'   => ['sometimes', 'string'],
            'address_line2'   => ['sometimes', 'string'],
            'address_state'   => ['sometimes', 'string'],
            'address_zip'     => ['sometimes', 'string'],
        ];
    }
}
