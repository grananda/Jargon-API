<?php

namespace App\Http\Requests\Organization;

use App\Http\Requests\Request;

class UpdateOrganizationInvitationRequest extends Request
{
    /**
     * Invitation token.
     *
     * @var string
     */
    public $invitationToken;

    /**
     * The Organization to be invited to.
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
        $this->invitationToken = $this->route('token');

        return true;
    }
}
