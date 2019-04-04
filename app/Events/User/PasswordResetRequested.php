<?php

namespace App\Events\User;

use App\Models\PasswordReset;

class PasswordResetRequested
{
    /**
     * @var \App\Models\PasswordReset
     */
    public $passwordReset;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\PasswordReset $passwordReset
     */
    public function __construct(PasswordReset $passwordReset)
    {
        $this->passwordReset = $passwordReset;
    }
}
