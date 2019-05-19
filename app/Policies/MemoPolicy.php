<?php

namespace App\Policies;

use App\Models\User;

class MemoPolicy extends AbstractPolicy
{
    /**
     * Determines is a user can list projects.
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
