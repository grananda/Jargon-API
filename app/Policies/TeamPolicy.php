<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Exception;

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

    /**
     * @param User             $user
     * @param \App\Models\Team $team
     *
     * @return bool
     */
    public function show(User $user, Team $team)
    {
        return $team->isMember($user) || $team->isOwner($user);
    }

    /**
     * @param User $user
     *
     * @throws Exception
     *
     * @return bool
     */
    public function create(User $user)
    {
        $subscriptionTeamCount = $user->activeSubscription->options()->where('option_key', 'team_count')->first()->option_value;

        $userTeamCount = $user->teams->filter(function ($team) use ($user) {
            /* @var $team \App\Models\Team */
            return $team->isOwner($user) == true;
        })->count();

        if ($subscriptionTeamCount <= $userTeamCount && ! is_null($subscriptionTeamCount)) {
            return false;
        }

        return true;
    }

    /**
     * @param User             $user
     * @param \App\Models\Team $team
     *
     * @return bool
     */
    public function update(User $user, Team $team)
    {
        return $team->isOwner($user);
    }

    /**
     * @param User             $user
     * @param \App\Models\Team $team
     *
     * @return bool
     */
    public function delete(User $user, Team $team)
    {
        return $team->isOwner($user);
    }
}
