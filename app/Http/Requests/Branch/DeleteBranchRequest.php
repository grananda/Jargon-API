<?php

namespace App\Http\Requests\Branch;

use App\Http\Requests\Request;
use App\Models\Translations\Project;

class DeleteBranchRequest extends Request
{
    /**
     * The Project model.
     *
     * @var \App\Models\Translations\Project
     */
    public $project;

    /**
     * The repository branch name.
     *
     * @var string
     */
    public $branch;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->project = Project::findByUuidOrFail($this->route('id'));

        $this->branch = $this->input('branch');

        return $this->user()->can('show', $this->project);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'branch' => ['required', 'string'],
        ];
    }
}
