<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use App\Models\User;

class DeleteUserRequest extends Request
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
        $this->user = User::findByUuidOrFail($this->route('id'));

        return $this->user()->can('delete', $this->user);
    }
}
