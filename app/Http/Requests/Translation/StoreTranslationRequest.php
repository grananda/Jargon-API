<?php

namespace App\Http\Requests\Translation;

use App\Http\Requests\Request;
use App\Models\Translations\Node;
use App\Models\Translations\Translation;
use App\Rules\ValidDialect;
use App\Rules\ValidProjectDialect;

class StoreTranslationRequest extends Request
{
    /**
     * The translation node.
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
        $this->node = Node::findByUuidOrFail($this->input('node'));

        return $this->user()->can('create', [Translation::class, $this->node->project]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'definition' => ['required', 'string'],
            'node'       => ['required'],
            'dialect'    => ['required', new ValidDialect(), new ValidProjectDialect($this->node->project)],
        ];
    }
}
