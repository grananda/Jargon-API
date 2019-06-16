<?php

namespace App\Http\Requests\Repository;

use App\Http\Requests\Request;
use App\Models\Translations\Project;

class IndexRepositoryRequest extends Request
{
    /**
     * The Project model.
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

        return $this->user()->can('show', [$this->project]);
    }
}
