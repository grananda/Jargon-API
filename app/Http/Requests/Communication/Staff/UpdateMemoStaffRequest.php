<?php

namespace App\Http\Requests\Communication\Staff;

use App\Http\Requests\Request;
use App\Models\Communications\Memo;
use Illuminate\Validation\Rule;

class UpdateMemoStaffRequest extends Request
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

        return $this->user()->can('staffUpdate', $this->memo);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'subject'      => ['required', 'string'],
            'body'         => ['required', 'string'],
            'status'       => ['required', Rule::in(['draft', 'sent'])],
            'recipients'   => ['array'],
            'recipients.*' => ['string'],
        ];
    }
}
