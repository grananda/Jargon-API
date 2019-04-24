<?php

namespace App\Http\Requests\Node;

use App\Http\Requests\Request;
use App\Models\Translations\Node;

/**
 * Class NodeCopyRequest.
 *
 * @package App\Http\Requests\Node
 */
class NodeCopyRequest extends Request
{
    /**
     * Node to copy.
     *
     * @var \App\Models\Translations\Node
     */
    public $node;

    /**
     * Parent node to copy to.
     *
     * @var \App\Models\Translations\Node
     */
    public $parentNode;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->node       = Node::findByUuidOrFail($this->input('node'));
        $this->parentNode = Node::findByUuidOrFail($this->input('parentNode'));

        if ($this->node->project->uuid != $this->parentNode->project->uuid) {
            return false;
        }
//        if ($this->node->uuid === $this->parentNode->uuid) return false;

        return $this->user()->can('update', $this->node->project);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'node'       => ['required', 'string'],
            'parentNode' => ['required', 'string'],
        ];
    }
}
