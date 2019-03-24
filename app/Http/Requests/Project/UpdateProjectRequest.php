<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\Request;
use App\Models\Translations\Project;
use App\Policies\Traits\ActiveSubscriptionRestrictionsTrait;
use App\Rules\ValidMember;
use App\Rules\ValidTeam;

class UpdateProjectRequest extends Request
{
    use ActiveSubscriptionRestrictionsTrait;

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

        /** @var array $collaborators */
        $collaborators = $this->input('collaborators') ?? [];

        $currentSubscriptionCollaborationQuota = $this->getCurrentSubscriptionCollaborationQuota($this->user()) + $this->project->members()->count();

        return $this->hasActiveSubscription($this->user())
            && $currentSubscriptionCollaborationQuota >= count($collaborators)
            && $this->user()->can('update', $this->project);
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
