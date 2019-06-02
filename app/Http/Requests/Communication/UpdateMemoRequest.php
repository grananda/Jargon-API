<?php

namespace App\Http\Requests\Communication;

use App\Http\Requests\Request;
use App\Models\Communications\Memo;

class UpdateMemoRequest extends Request
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

        return $this->user()->can('update', $this->memo);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'is_read' => ['required', 'boolean'],
        ];
    }
}
