<?php

namespace App\Policies;

use App\Models\User;

class TeamPolicy extends AbstractPolicy
{
    /**
     * Determines is a user can list teams.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function list(User $user)
    {
        return true;
    }
}
