<?php

namespace App\Http\Requests\Plugin;

use App\Models\Translations\Project;
use Illuminate\Foundation\Http\FormRequest;

class StoreApiPluginSyncRequest extends FormRequest
{
    /**
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'project' => ['required', 'string'],
            'data'    => ['required', 'json'],
        ];
    }
}
