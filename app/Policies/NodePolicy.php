<?php

namespace App\Policies;

use App\Models\Translations\Node;
use App\Models\Translations\Project;
use App\Models\User;
use App\Policies\Traits\NodeCopyRestrictionsTrait;

class NodePolicy extends AbstractPolicy
{
    use NodeCopyRestrictionsTrait;

    /**
     * Determines is a user can list nodes within a project.
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
     * Determines is a user can create nodes within a project.
     *
     * @param User    $user
     * @param Project $project
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
     * @param User $user
     * @param Node $node
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
     * @param User $user
     * @param Node $node
     *
     * @return bool
     */
    public function update(User $user, Node $node)
    {
        return $node->project->isCollaborator($user)
            && $user->hasRoles([Project::PROJECT_OWNER_ROLE_ALIAS, Project::PROJECT_MANAGER_ROLE_ALIAS]);
    }

    /**
     * Determines is a user can copy a node within a project.
     *
     * @param User $user
     * @param Node $node
     * @param Node $parent
     *
     * @return bool
     */
    public function copy(User $user, Node $node, Node $parent)
    {
        return $node->project->isCollaborator($user)
            && $user->hasRoles([Project::PROJECT_OWNER_ROLE_ALIAS, Project::PROJECT_MANAGER_ROLE_ALIAS])
            && $this->nodesBelongToSameProject($node, $parent)
            && $this->nodesAreDifferent($node, $parent)
            && $this->nodesBelongToDifferentBranch($node, $parent)
            && $this->parentNodesAreDifferent($node, $parent);
    }

    /**
     * Determines is a user can relocate a  node within a project.
     *
     * @param User $user
     * @param Node $node
     * @param Node $parent
     *
     * @return bool
     */
    public function relocate(User $user, Node $node, Node $parent)
    {
        return $node->project->isCollaborator($user)
            && $user->hasRoles([Project::PROJECT_OWNER_ROLE_ALIAS, Project::PROJECT_MANAGER_ROLE_ALIAS])
            && $this->nodesBelongToSameProject($node, $parent)
            && $this->nodesAreDifferent($node, $parent)
            && $this->nodesBelongToDifferentBranch($node, $parent)
            && $this->parentNodesAreDifferent($node, $parent);
    }
}
