<?php

namespace App\Http\Requests\Organization;

use App\Http\Requests\Request;
use App\Models\Organization;

class EditOrganizationRequest extends Request
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
}
