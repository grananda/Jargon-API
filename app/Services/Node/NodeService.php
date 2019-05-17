<?php

namespace App\Services\Node;

use App\Models\Translations\Node;
use App\Models\Translations\Project;
use App\Repositories\NodeRepository;
use App\Services\Traits\NodeToolsTrait;

/**
 * Class NodeService.
 *
 * @package App\Services\Node
 */
class NodeService
{
    use NodeToolsTrait;

    /**
     * The NodeRepository instance.
     *
     * @var \App\Repositories\NodeRepository
     */
    protected $nodeRepository;

    /**
     * NodeService constructor.
     *
     * @param \App\Repositories\NodeRepository $nodeRepository
     */
    public function __construct(NodeRepository $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * Store a sibling node.
     *
     * @param \App\Models\Translations\Project $project
     * @param \App\Models\Translations\Node    $parentNode
     * @param array                            $attributes
     *
     * @throws \Throwable
     *
     * @return \App\Models\Translations\Node
     */
    public function storeNode(Project $project, ?Node $parentNode, array $attributes): Node
    {
        /** @var \App\Models\Translations\Node $node */
        $node = $this->nodeRepository->createNode($project, $parentNode, $attributes);

        $parentNode = $parentNode ?? $node;

        $parentNode->load('children');

        $this->normalizeChildNodesSortIndex($parentNode);

        return $node->fresh();
    }

    /**
     * @param \App\Models\Translations\Node $node
     * @param array                         $attributes
     *
     * @throws \Throwable
     *
     * @return \App\Models\Translations\Node
     */
    public function updateNode(Node $node, array $attributes): Node
    {
        /** @var \App\Models\Translations\Node $node */
        $node = $this->nodeRepository->update($node, $attributes);

        return $this->rebuildNodeBranchRoute($node);
    }

    /**
     * Deletes a node.
     *
     * @param \App\Models\Translations\Node $node
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function deleteNode(Node $node): void
    {
        /** @var \App\Models\Translations\Node $rootNode */
        $rootNode = $node->parent ?? null;

        $this->nodeRepository->delete($node);

        if ($rootNode) {
            $rootNode->load('children');

            $this->normalizeChildNodesSortIndex($rootNode);
        } else {
            /** @var \App\Models\Translations\Project $project */
            $project = $node->project;

            $this->normalizeChildRootNodesSortIndex($project);
        }
    }
}
