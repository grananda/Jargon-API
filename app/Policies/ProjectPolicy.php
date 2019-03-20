<?php

namespace App\Policies;

use App\Models\Translations\Project;
use App\Models\User;
use Exception;

class ProjectPolicy extends AbstractPolicy
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

    /**
     * @param User                             $user
     * @param \App\Models\Translations\Project $project
     *
     * @return bool
     */
    public function show(User $user, Project $project)
    {
        return $project->isMember($user) || $project->isOwner($user);
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
        $subscriptionProjectCount = $user->activeSubscription->options()->where('option_key', 'project_count')->first()->option_value;

        $currentProjectCount = $user->organizations->filter(function ($org) use ($user) {
            /* @var $org \App\Models\Organization */
            return $org->isOwner($user) == true;
        })->count();

        if ($subscriptionProjectCount <= $currentProjectCount && ! is_null($subscriptionProjectCount)) {
            return false;
        }

        return true;
    }

    /**
     * @param User                             $user
     * @param \App\Models\Translations\Project $project
     *
     * @return bool
     */
    public function update(User $user, Project $project)
    {
        return $project->isOwner($user);
    }

    /**
     * @param User                             $user
     * @param \App\Models\Translations\Project $project
     *
     * @return bool
     */
    public function delete(User $user, Project $project)
    {
        return $project->isOwner($user);
    }
}
