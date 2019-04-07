<?php

namespace App\Http\Requests\Account;

use App\Http\Requests\Request;
use App\Models\User;

class ResendAccountActivationRequest extends Request
{
    /**
     * @var \App\Models\User
     */
    public $user;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->user = User::where('email', $this->input('email'))->firstOrFail();

        return ! $this->user->isActivated();
    }
}
