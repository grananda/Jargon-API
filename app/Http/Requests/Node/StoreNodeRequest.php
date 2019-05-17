<?php

namespace App\Http\Requests\Node;

use App\Http\Requests\Request;
use App\Models\Translations\Node;
use App\Models\Translations\Project;
use App\Rules\ValidProjectNode;

/**
 * Class StoreNodeRequest.
 *
 * @package App\Http\Requests\Node
 */
class StoreNodeRequest extends Request
{
    /**
     * The Node instance.
     *
     * @var \App\Models\Translations\Node
     */
    public $parentNode;

    /**
     * The node Project instance.
     *
     * @var \App\Models\Translations\Project
     */
    public $project;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $parent = $this->input('parent', null);

        $this->parentNode = is_null($parent) ? $parent : Node::findByUuid($parent);

        $this->project = Project::findByUuidOrFail($this->input('project'));

        return $this->user()->can('create', [Node::class, $this->project]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'project' => ['required', 'string'],
            'parent'  => ['nullable', 'string', new ValidProjectNode($this->project)],
        ];
    }
}
