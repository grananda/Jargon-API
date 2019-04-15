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

    /**
     * @param User $user
     *
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->hasRole(User::SENIOR_STAFF_MEMBER);
    }

    /**
     * @param \App\Models\User $authUser
     * @param User             $user
     *
     * @return bool
     */
    public function update(User $authUser, User $user)
    {
        return $user->hasRole(User::SENIOR_STAFF_MEMBER) || $authUser->uuid === $user->uuid;
    }

    /**
     * @param \App\Models\User $authUser
     * @param User             $user
     *
     * @return bool
     */
    public function cancel(User $authUser, User $user)
    {
        return $authUser->uuid === $user->uuid;
    }
}
