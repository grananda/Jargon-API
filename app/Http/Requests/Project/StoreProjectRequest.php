<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\Request;
use App\Models\Translations\Project;
use App\Rules\ValidMember;
use App\Rules\ValidTeam;

class StoreProjectRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var array $collaborators */
        $collaborators = $this->input('collaborators') ?? [];

        return $this->user()->can('create', [Project::class, count($collaborators)]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'           => ['required', 'string'],
            'description'     => ['nullable', 'string', 'max:255'],
            'collaborators'   => ['array'],
            'collaborators.*' => ['array', new ValidMember()],
            'teams'           => ['array'],
            'teams.*'         => ['array', new ValidTeam($this->user())],
        ];
    }
}
