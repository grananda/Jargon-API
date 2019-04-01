<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends AbstractPolicy
{
    /**
     * @param User $user
     *
     * @return bool
     */
    public function deactivate(User $user)
    {
        return $user->hasRole(User::SENIOR_STAFF_MEMBER);
    }
}
