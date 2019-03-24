<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use App\Policies\Traits\ActiveSubscriptionRestrictionsTrait;

class TeamPolicy extends AbstractPolicy
{
    use ActiveSubscriptionRestrictionsTrait;

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
     * @param int  $collaboratorsSize
     *
     * @return bool
     */
    public function create(User $user, int $collaboratorsSize)
    {
        return $this->hasActiveSubscription($user)
            && (bool) $this->getCurrentSubscriptionTeamQuota($user)
            && $this->getCurrentSubscriptionCollaborationQuota($user) >= $collaboratorsSize;
    }

    /**
     * @param User             $user
     * @param \App\Models\Team $team
     * @param int              $collaboratorsSize
     *
     * @return bool
     */
    public function update(User $user, Team $team, int $collaboratorsSize)
    {
        $currentSubscriptionCollaborationQuota = $this->getCurrentSubscriptionCollaborationQuota($user) + $team->members()->count();

        return $this->hasActiveSubscription($user)
            && (bool) $team->isOwner($user)
            && $currentSubscriptionCollaborationQuota >= $collaboratorsSize;
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
