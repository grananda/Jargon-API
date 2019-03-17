<?php

namespace App\Http\Requests\Team;

use App\Http\Requests\Request;
use App\Models\Team;
use App\Policies\Traits\ActiveSubscriptionRestrictionsTrait;
use App\Rules\ValidMember;

class UpdateTeamRequest extends Request
{
    use ActiveSubscriptionRestrictionsTrait;

    /**
     * The Team instance.
     *
     * @var \App\Models\Team
     */
    public $team;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->team = Team::findByUuidOrFail($this->route('id'));

        /** @var array $collaborators */
        $collaborators = $this->input('collaborators');

        $currentSubscriptionCollaborationQuota = $this->getCurrentSubscriptionCollaborationQuota($this->user()) + $this->team->members()->count();

        return $this->hasActiveSubscription($this->user())
            && $currentSubscriptionCollaborationQuota >= count($collaborators)
            && $this->user()->can('update', $this->team);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'            => ['required', 'string'],
            'description'     => ['nullable', 'string', 'max:255'],
            'collaborators'   => ['array'],
            'collaborators.*' => ['array', new ValidMember()],
        ];
    }
}
