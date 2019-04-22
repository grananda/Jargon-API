<?php

namespace App\Http\Requests\Card;

use App\Http\Requests\Request;
use App\Models\Card;

class IndexCardRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('list', Card::class);
    }
}
