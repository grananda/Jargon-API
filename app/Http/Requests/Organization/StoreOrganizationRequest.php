<?php

namespace App\Http\Requests\Organization;

use App\Http\Requests\Request;
use App\Models\Organization;
use App\Policies\Traits\ActiveSubscriptionRestrictionsTrait;
use App\Rules\ValidMember;
use App\Rules\ValidTeam;

class StoreOrganizationRequest extends Request
{
    use ActiveSubscriptionRestrictionsTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var array $collaborators */
        $collaborators = $this->input('collaborators');

        return $this->hasActiveSubscription($this->user())
            && $this->getCurrentSubscriptionCollaborationQuota($this->user()) >= count($collaborators)
            && $this->user()->can('create', Organization::class);
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
            'teams'           => ['array'],
            'teams.*'         => [new ValidTeam($this->user())],
            'collaborators'   => ['array'],
            'collaborators.*' => ['array', new ValidMember()],
        ];
    }
}
