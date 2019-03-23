<?php

namespace App\Policies\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

trait ActiveSubscriptionRestrictionsTrait
{
    /**
     * Determines if current entity can hold desired collaborator count.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function getCurrentSubscriptionCollaborationQuota(User $user)
    {
        if ($activeSubscription = $user->activeSubscription) {
            $subscriptionCollaboratorQuota = $activeSubscription->options()
                ->where('option_key', 'collaborator_count')
                ->first()
                ->option_value;

            $subscriptionCollaboratorQuota -= $this->calculateCurrentOrganizationQuota($user->organizations);
            $subscriptionCollaboratorQuota -= $this->calculateCurrentTeamQuota($user->teams);
            $subscriptionCollaboratorQuota -= $this->calculateCurrentProjectQuota($user->projects);

            return $subscriptionCollaboratorQuota;
        }

        return false;
    }

    /**
     * Returns user subscription team quota.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function getCurrentSubscriptionTeamQuota(User $user)
    {
        if ($activeSubscription = $user->activeSubscription) {
            $subscriptionTeamQuota = $activeSubscription->options()
                ->where('option_key', 'team_count')
                ->first()
                ->option_value;

            $subscriptionTeamQuota -= $user->teams()->count();

            return $subscriptionTeamQuota;
        }

        return false;
    }

    /**
     * Returns user subscription project quota.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function getCurrentSubscriptionProjectQuota(User $user)
    {
        if ($activeSubscription = $user->activeSubscription) {
            $subscriptionProjectQuota = $activeSubscription->options()
                ->where('option_key', 'project_count')
                ->first()
                ->option_value;

            $subscriptionProjectQuota -= $user->projects()->count();

            return $subscriptionProjectQuota;
        }

        return false;
    }

    /**
     * Determines if user has current subscription.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function hasActiveSubscription(User $user)
    {
        return (bool) $user->activeSubscription;
    }

    /**
     * Calculates current user organization collaborators.
     *
     * @param \Illuminate\Database\Eloquent\Collection $organizations
     *
     * @return int
     */
    private function calculateCurrentOrganizationQuota(Collection $organizations)
    {
        $counter = 0;

        /** @var \App\Models\Organization $organization */
        foreach ($organizations as $organization) {
            $counter += $organization->members()->count();
        }

        return $counter;
    }

    /**
     * Calculates current user team collaborators.
     *
     * @param \Illuminate\Database\Eloquent\Collection $teams
     *
     * @return int
     */
    private function calculateCurrentTeamQuota(Collection $teams)
    {
        $counter = 0;

        /** @var \App\Models\Team $team */
        foreach ($teams as $team) {
            $counter -= $team->members()->count();
        }

        return $counter;
    }

    /**
     * Calculates current user project collaborators.
     *
     * @param \Illuminate\Database\Eloquent\Collection $projects
     *
     * @return int
     */
    private function calculateCurrentProjectQuota(Collection $projects)
    {
        $counter = 0;

        /** @var \App\Models\Translations\Project $project */
        foreach ($projects as $project) {
            $counter += $project->members()->count();
        }

        return $counter;
    }
}
