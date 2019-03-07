<?php

namespace App\Http\Requests\Organization;

use App\Http\Requests\Request;
use App\Models\Organization;
use App\Rules\ValidMember;
use App\Rules\ValidTeam;

class StoreOrganizationRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Organization::class);
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
