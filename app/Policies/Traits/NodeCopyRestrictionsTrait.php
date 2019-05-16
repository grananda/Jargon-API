<?php

namespace App\Policies\Traits;

use App\Models\Translations\Node;

trait NodeCopyRestrictionsTrait
{
    /**
     * Checks if two nodes belong to the same project.
     *
     * @param \App\Models\Translations\Node $node
     * @param \App\Models\Translations\Node $parent
     *
     * @return bool
     */
    public function nodesBelongToSameProject(Node $node, Node $parent)
    {
        return $node->project->uuid === $parent->project->uuid;
    }

    /**
     * Check is two nodes are the same.
     *
     * @param \App\Models\Translations\Node $node
     * @param \App\Models\Translations\Node $parent
     *
     * @return bool
     */
    public function nodesAreDifferent(Node $node, Node $parent)
    {
        return ! $node->is($parent);
    }

    /**
     * Check is the target parent node and actual parent node are not the same.
     *
     * @param \App\Models\Translations\Node $node
     * @param \App\Models\Translations\Node $parent
     *
     * @return bool
     */
    public function parentNodesAreDifferent(Node $node, Node $parent)
    {
        /** @var Node $nodeParent */
        $nodeParent = $node->isRoot() ? $node : $node->parent;

        return ! $parent->is($nodeParent);
    }

    /**
     * Check that nodes do not belong to the sme branch.
     *
     * @param \App\Models\Translations\Node $node
     * @param \App\Models\Translations\Node $parent
     *
     * @return bool
     */
    public function nodesBelongToDifferentBranch(Node $node, Node $parent)
    {
        return ! $parent->isDescendantOf($node);
    }
}
