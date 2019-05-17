<?php

namespace App\Events\User;

use App\Models\User;

class UserWasDeleted
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
