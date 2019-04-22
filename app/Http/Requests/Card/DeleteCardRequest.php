<?php

namespace App\Http\Requests\Card;

use App\Http\Requests\Request;
use App\Models\Card;

class DeleteCardRequest extends Request
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

        return $this->user()->can('delete', $this->card);
    }
}
