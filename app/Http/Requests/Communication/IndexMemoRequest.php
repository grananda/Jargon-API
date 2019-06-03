<?php

namespace App\Http\Requests\Communication;

use App\Http\Requests\Request;
use App\Models\Communications\Memo;

class IndexMemoRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('list', [Memo::class]);
    }
}
