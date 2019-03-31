<?php

namespace App\Http\Requests\Option;

use App\Http\Requests\Request;
use App\Models\Options\Option;
use App\Rules\ValidOptionCategory;
use Illuminate\Validation\Rule;

class UpdateOptionRequest extends Request
{
    /**
     * The Option instance.
     *
     * @var \App\Models\Options\Option
     */
    public $option;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->option = Option::findByUuidOrFail($this->route('id'));

        return $this->user()->can('update', $this->option);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'              => ['sometimes', 'string'],
            'description'        => ['sometimes', 'string', 'max:255'],
            'option_value'       => ['sometimes'],
            'option_category_id' => ['sometimes', new ValidOptionCategory()],
            'option_key'         => ['sometimes', Rule::in([null])],
            'option_type'        => ['sometimes', Rule::in([null])],
            'option_enum'        => ['sometimes', Rule::in([null])],
            'option_scope'       => ['sometimes', Rule::in([null])],
        ];
    }
}
