<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\Request;
use App\Models\Translations\Project;

class DeleteProjectRequest extends Request
{
    /** @var \App\Models\Translations\Project */
    public $project;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->project = Project::findByUuidOrFail($this->route('id'));

        return $this->user()->can('delete', $this->project);
    }
}
