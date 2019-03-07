<?php

namespace App\Http\Requests\Organization;

use App\Http\Requests\Request;
use App\Models\Organization;

class CreateOrganizationRequest extends Request
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
}
