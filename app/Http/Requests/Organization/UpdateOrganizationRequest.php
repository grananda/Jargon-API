<?php

namespace App\Http\Requests\Organization;

use App\Http\Requests\Request;
use App\Models\Organization;
use App\Policies\Traits\ActiveSubscriptionRestrictionsTrait;
use App\Rules\ValidMember;
use App\Rules\ValidTeam;

class UpdateOrganizationRequest extends Request
{
    use ActiveSubscriptionRestrictionsTrait;

    /**
     * The organization instance.
     *
     * @var \App\Models\Organization
     */
    public $organization;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->organization = Organization::findByUuidOrFail($this->route('id'));

        /** @var array $collaborators */
        $collaborators = $this->input('collaborators');

        $currentSubscriptionCollaborationQuota = $this->getCurrentSubscriptionCollaborationQuota($this->user()) + $this->organization->members()->count();

        return $this->hasActiveSubscription($this->user())
            && $currentSubscriptionCollaborationQuota >= count($collaborators)
            && $this->user()->can('update', $this->organization);
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
            'teams'           => ['array'],
            'teams.*'         => [new ValidTeam($this->user())],
            'collaborators'   => ['array'],
            'collaborators.*' => ['array', new ValidMember()],
        ];
    }
}
