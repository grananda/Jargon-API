<?php

namespace App\Http\Requests\Option;

use App\Http\Requests\Request;
use App\Models\Options\Option;
use App\Rules\ValidOptionCategory;
use Illuminate\Validation\Rule;

class StoreOptionRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Option::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'              => ['required', 'string'],
            'description'        => ['required', 'string', 'max:255'],
            'option_category_id' => ['required', new ValidOptionCategory()],
            'option_key'         => ['required', 'string'],
            'option_value'       => ['required'],
            'option_scope'       => ['required', 'string', Rule::in(['user', 'staff'])],
            'option_type'        => ['required', 'string', Rule::in(['check', 'text'])],
            'option_enum'        => ['sometimes', 'array'],
        ];
    }
}
