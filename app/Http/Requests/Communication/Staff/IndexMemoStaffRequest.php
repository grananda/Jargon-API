<?php

namespace App\Http\Requests\Communication\Staff;

use App\Http\Requests\Request;
use App\Models\Communications\Memo;

class IndexMemoStaffRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('staffList', [Memo::class]);
    }
}
