<?php

namespace App\Http\Requests\Option;

use App\Http\Requests\Request;
use App\Models\Options\Option;

class DeleteOptionRequest extends Request
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

        return $this->user()->can('delete', $this->option);
    }
}
