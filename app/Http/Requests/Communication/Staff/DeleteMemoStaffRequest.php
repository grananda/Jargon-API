<?php

namespace App\Http\Requests\Communication\Staff;

use App\Http\Requests\Request;
use App\Models\Communications\Memo;

class DeleteMemoStaffRequest extends Request
{
    /**
     * The Memo instance.
     *
     * @var \App\Models\Communications\Memo
     */
    public $memo;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->memo = Memo::findByUuidOrFail($this->route('id'));

        return $this->user()->can('staffDelete', $this->memo);
    }
}
