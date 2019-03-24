<?php

namespace App\Http\Requests\Team;

use App\Http\Requests\Request;
use App\Models\Team;
use App\Policies\Traits\ActiveSubscriptionRestrictionsTrait;
use App\Rules\ValidMember;

class StoreTeamRequest extends Request
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
        $collaborators = $this->input('collaborators') ?? [];

        return $this->hasActiveSubscription($this->user())
            && $this->getCurrentSubscriptionCollaborationQuota($this->user()) >= count($collaborators)
            && $this->user()->can('create', Team::class);
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
