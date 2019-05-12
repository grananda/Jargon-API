<?php

namespace App\Policies;

use App\Models\Translations\Project;
use App\Models\User;
use App\Policies\Traits\ActiveSubscriptionRestrictionsTrait;

class TranslationPolicy extends AbstractPolicy
{
    use ActiveSubscriptionRestrictionsTrait;

    /**
     * Determines is a user can list translations within a project.
     *
     * @param User    $user
     * @param Project $project
     *
     * @return bool
     */
    public function list(User $user, Project $project)
    {
        return $project->isCollaborator($user);
    }

    /**
     * Determines is a user can show a translation within a project.
     *
     * @param User    $user
     * @param Project $project
     *
     * @return bool
     */
    public function show(User $user, Project $project)
    {
        return $project->isCollaborator($user);
    }

    /**
     * Determines is a user can create translations within a project.
     *
     * @param User    $user
     * @param Project $project
     *
     * @return bool
     */
    public function create(User $user, Project $project)
    {
        return $project->isCollaborator($user)
            && $user->hasRoles([Project::PROJECT_OWNER_ROLE_ALIAS, Project::PROJECT_MANAGER_ROLE_ALIAS])
            && (bool) $user->getCurrentSubscriptionTranslationQuota();
    }

    /**
     * Determines is a user can delete translations within a project.
     *
     * @param User    $user
     * @param Project $project
     *
     * @return bool
     */
    public function delete(User $user, Project $project)
    {
        return $project->isCollaborator($user)
            && $user->hasRoles([Project::PROJECT_OWNER_ROLE_ALIAS, Project::PROJECT_MANAGER_ROLE_ALIAS]);
    }

    /**
     * Determines is a user can update translations within a project.
     *
     * @param User    $user
     * @param Project $project
     *
     * @return bool
     */
    public function update(User $user, Project $project)
    {
        return $project->isCollaborator($user);
    }
}
