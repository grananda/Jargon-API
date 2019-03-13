<?php

namespace App\Http\Requests\Organization;

use App\Http\Requests\Request;
use App\Models\Organization;

class ShowOrganizationRequest extends Request
{
    /**
     * The Organization instance.
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
        $organizationId = (string) $this->route('id');

        $this->organization = Organization::findByUuidOrFail($organizationId);

        return $this->user()->can('show', [Organization::class, $this->organization]);
    }
}
