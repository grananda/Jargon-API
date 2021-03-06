<?php

namespace App\Http\Requests\Node;

use App\Http\Requests\Request;
use App\Models\Translations\Node;

/**
 * Class UpdateNodeRequest.
 *
 * @package App\Http\Requests\Node
 */
class UpdateNodeRequest extends Request
{
    /**
     * The Node instance.
     *
     * @var \App\Models\Translations\Node
     */
    public $node;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->node = Node::findByUuidOrFail($this->route('id'));

        return $this->user()->can('update', $this->node);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'key' => ['required', 'string'],
        ];
    }
}
