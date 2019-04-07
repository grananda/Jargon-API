<?php

namespace App\Events\User;

use App\Models\User;

class UserWasDeactivated
{
    /**
     * @var \App\Models\User
     */
    public $user;

    /**
     * UserWasCreated constructor.
     *
     * @param \App\Models\User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
