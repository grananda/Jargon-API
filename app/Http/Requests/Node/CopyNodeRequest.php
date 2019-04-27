<?php

namespace App\Http\Requests\Node;

use App\Http\Requests\Request;
use App\Models\Translations\Node;

/**
 * Class UpdateNodeRequest.
 *
 * @package App\Http\Requests\Node
 */
class CopyNodeRequest extends Request
{
    /**
     * The node instance to move.
     *
     * @var \App\Models\Translations\Node
     */
    public $node;

    /**
     * The new parent node.
     *
     * @var \App\Models\Translations\Node
     */
    public $parent;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->node = Node::findByUuidOrFail($this->route('id'));

        $this->parent = Node::findByUuidOrFail($this->input('parent'));

        return $this->user()->can('copy', [$this->node, $this->parent]);
    }
}
