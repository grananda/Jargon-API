<?php

namespace App\Http\Requests\Node;

use App\Http\Requests\Request;
use App\Models\Translations\Node;
use App\Models\Translations\Project;

/**
 * Class IndexNodeRequest.
 *
 * @package App\Http\Requests\Node
 */
class IndexNodeRequest extends Request
{
    /**
     * The nodes parent projects.
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
        $this->project = Project::findByUuidOrFail($this->route('id'));

        return $this->user()->can('list', [Node::class, $this->project]);
    }
}
