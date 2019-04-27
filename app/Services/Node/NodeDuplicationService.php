<?php

namespace App\Services\Node;

use App\Models\Translations\Node;
use App\Services\Traits\NodeToolsTrait;

class NodeDuplicationService
{
    use NodeToolsTrait;

    /**
     * Copies a node into a new parent.
     *
     * @param \App\Models\Translations\Node $node
     * @param \App\Models\Translations\Node $parent
     *
     * @throws \Throwable
     *
     * @return \App\Models\Translations\Node|null
     */
    public function copyNode(Node $node, Node $parent)
    {
        $parent->appendNode($node);

        $this->rebuildNodeBranchRoute($parent);

        /** @var \App\Models\Translations\Node $rootNode */
        $rootNode = $parent->parent ?? null;

        if ($rootNode) {
            $rootNode->load('children');

            $this->normalizeChildNodesSortIndex($rootNode);
        } else {
            /** @var \App\Models\Translations\Project $project */
            $project = $node->project;

            $this->normalizeChildRootNodesSortIndex($project);
        }

        return $node->fresh();
    }
}
