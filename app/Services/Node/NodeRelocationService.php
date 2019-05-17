<?php

namespace App\Services\Node;

use App\Models\Translations\Node;
use App\Services\Traits\NodeToolsTrait;
use Throwable;

class NodeRelocationService
{
    use NodeToolsTrait;

    /**
     * Relocates a node to a new parent node.
     *
     * @param Node $node
     * @param Node $parent
     *
     * @throws Throwable
     *
     * @return Node
     */
    public function relocateNode(Node $node, Node $parent): Node
    {
        $parent->appendNode($node);

        $parent->fresh();

        $this->rebuildNodeBranchRoute($parent);

        $this->normalizeChildRootNodesSortIndex($parent->project);
        $this->normalizeChildNodesSortIndex($parent->fresh());

        return $parent->fresh();
    }
}
