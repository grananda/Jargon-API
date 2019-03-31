<?php

namespace App\Http\Requests\Option;

use App\Http\Requests\Request;
use App\Models\Options\Option;

class IndexOptionRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('list', Option::class);
    }
}
