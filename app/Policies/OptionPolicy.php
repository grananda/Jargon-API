<?php

namespace App\Policies;

use App\Models\Options\Option;
use App\Models\User;

class OptionPolicy extends AbstractPolicy
{
    /**
     * @param User $user
     *
     * @return bool
     */
    public function list(User $user)
    {
        return true;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasRole(User::SENIOR_STAFF_MEMBER);
    }

    /**
     * @param User                       $user
     * @param \App\Models\Options\Option $option
     *
     * @return bool
     */
    public function update(User $user, Option $option)
    {
        return $user->hasRole(User::SENIOR_STAFF_MEMBER);
    }

    /**
     * @param User                       $user
     * @param \App\Models\Options\Option $option
     *
     * @return bool
     */
    public function delete(User $user, Option $option)
    {
        return $user->hasRole(User::SENIOR_STAFF_MEMBER);
    }
}
