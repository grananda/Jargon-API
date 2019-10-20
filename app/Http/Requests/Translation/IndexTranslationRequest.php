<?php

namespace App\Http\Requests\Translation;

use App\Http\Requests\Request;
use App\Models\Translations\Node;
use App\Models\Translations\Translation;

class IndexTranslationRequest extends Request
{
    /**
     * The translation parent node.
     *
     * @var Node
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

        return $this->user()->can('list', [Translation::class, $this->node->project]);
    }
}
