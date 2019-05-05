<?php

namespace App\Http\Requests\Node;

use App\Http\Requests\Request;
use App\Models\Translations\Node;

class RelocateNodeRequest extends Request
{
    /**
     * The node instance to move.
     *
     * @var Node
     */
    public $node;

    /**
     * The new parent node.
     *
     * @var Node
     */
    public $parent;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->node   = Node::findByUuidOrFail($this->route('id'));
        $this->parent = Node::findByUuidOrFail($this->input('parent'));

        return $this->user()->can('relocate', [$this->node, $this->parent]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'parent' => ['required', 'string'],
        ];
    }
}
