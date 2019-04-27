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
        /** @var \App\Models\Translations\Node $new */
        $new = $this->replicateNodeTree($node);

        $parent->appendNode($new);

        $parent->fresh();

        $this->rebuildNodeBranchRoute($parent);

        $this->normalizeChildNodesSortIndex($parent);

        return $parent->fresh();
    }

    /**
     * Replicates a node and its children into a brand new tree structure.
     *
     * @param \App\Models\Translations\Node $node
     *
     * @throws \Throwable
     *
     * @return \App\Models\Translations\Node
     */
    private function replicateNodeTree(Node $node): Node
    {
        /** @var \App\Models\Translations\Node $new */
        $new = $node->copy();

        $this->copyChildren($node, $new);

        return $new;
    }

    /**
     * Recursively copies a node children.
     *
     * @param Node $node
     * @param Node $parent
     *
     * @throws \Throwable
     */
    private function copyChildren(Node $node, Node $parent): void
    {
        $node->load('children');

        /** @var Node $childNode */
        foreach ($node->children as $childNode) {
            $childNode->load('children');

            /** @var \App\Models\Translations\Node $newChild */
            $newChild = $childNode->copy();
            $parent->appendNode($newChild);

            if ($childNode->children()->count() > 0) {
                $this->copyChildren($childNode, $newChild);
            }
        }
    }
}
