<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\Request;

class UpdateProjectInvitationRequest extends Request
{
    /**
     * Invitation token.
     *
     * @var string
     */
    public $invitationToken;

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
