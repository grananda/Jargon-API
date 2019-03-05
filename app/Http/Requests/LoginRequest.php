<?php

namespace App\Http\Requests;

class LoginRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'       => ['required', 'email'],
            'password'    => ['required', 'min:6'],
            'remember_me' => ['sometimes', 'boolean'],
        ];
    }
}
