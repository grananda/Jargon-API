<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;
use App\Models\User;

class UserCancellationRequest extends Request
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
        $this->user = User::findByUuidOrFail($this->input('id'));

        return $this->user()->can('cancel', $this->user);
    }
}
