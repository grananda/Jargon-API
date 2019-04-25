<?php

namespace App\Policies;

use App\Models\Translations\Node;
use App\Models\Translations\Project;
use App\Models\User;

class NodePolicy extends AbstractPolicy
{
    /**
     * Determines is a user can list nodes within a project.
     *
     * @param \App\Models\User                 $user
     * @param \App\Models\Translations\Project $project
     *
     * @return bool
     */
    public function list(User $user, Project $project)
    {
        return $project->isCollaborator($user);
    }

    /**
     * Determines is a user can create nodes within a project.
     *
     * @param \App\Models\User                 $user
     * @param \App\Models\Translations\Project $project
     *
     * @return bool
     */
    public function create(User $user, Project $project)
    {
        return $project->isCollaborator($user)
            && $user->hasRoles([Project::PROJECT_OWNER_ROLE_ALIAS, Project::PROJECT_MANAGER_ROLE_ALIAS]);
    }

    /**
     * Determines is a user can delete nodes within a project.
     *
     * @param \App\Models\User              $user
     * @param \App\Models\Translations\Node $node
     *
     * @return bool
     */
    public function delete(User $user, Node $node)
    {
        return $node->project->isCollaborator($user)
            && $user->hasRoles([Project::PROJECT_OWNER_ROLE_ALIAS, Project::PROJECT_MANAGER_ROLE_ALIAS]);
    }

    /**
     * Determines is a user can update nodes within a project.
     *
     * @param \App\Models\User              $user
     * @param \App\Models\Translations\Node $node
     *
     * @return bool
     */
    public function update(User $user, Node $node)
    {
        return $node->project->isCollaborator($user)
            && $user->hasRoles([Project::PROJECT_OWNER_ROLE_ALIAS, Project::PROJECT_MANAGER_ROLE_ALIAS]);
    }
}
