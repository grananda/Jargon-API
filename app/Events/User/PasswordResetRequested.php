<?php

namespace App\Events\User;

use App\Models\User;

class PasswordResetRequested
{
    /**
     * @var \App\Models\USer
     */
    public $user;

    /**
     * @var string
     */
    public $token;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\User $user
     * @param string           $token
     */
    public function __construct(User $user, string $token)
    {
        $this->user = $user;

        $this->token = $token;
    }
}
