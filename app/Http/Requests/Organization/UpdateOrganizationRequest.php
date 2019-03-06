<?php

namespace App\Http\Requests\Organization;

use App\Http\Requests\Request;
use App\Models\Organization;
use App\Rules\ValidCollaborator;
use App\Rules\ValidTeam;

class UpdateOrganizationRequest extends Request
{
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

        return $this->user()->can('update', $this->organization);
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
            'collaborators.*' => ['array', new ValidCollaborator()],
        ];
    }
}
