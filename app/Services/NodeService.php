<?php

namespace App\Services;

use App\Models\Translations\Node;
use App\Models\Translations\Project;
use App\Repositories\NodeRepository;

/**
 * Class NodeService.
 *
 * @package App\Services\Node
 */
class NodeService
{
    /**
     * The NodeRepository instance.
     *
     * @var \App\Repositories\NodeRepository
     */
    protected $nodeRepository;

    /**
     * The TranslationNodeSortingService instance.
     *
     * @var \App\Services\NodeSortingService
     */
    protected $nodeSortingService;

    /**
     * NodeService constructor.
     *
     * @param \App\Repositories\NodeRepository $nodeRepository
     * @param \App\Services\NodeSortingService $nodeSortingService
     */
    public function __construct(NodeRepository $nodeRepository, NodeSortingService $nodeSortingService)
    {
        $this->nodeRepository     = $nodeRepository;
        $this->nodeSortingService = $nodeSortingService;
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

        $this->nodeSortingService->normalizeChildNodesSortIndex($parentNode);

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
        /** @var \App\Models\Translations\Project $project */
        $project = $node->project;

        /** @var \App\Models\Translations\Node $rootNode */
        $rootNode = $node->parent ?? null;

        $this->nodeRepository->delete($node);

        if ($rootNode) {
            $rootNode->load('children');

            $this->nodeSortingService->normalizeChildNodesSortIndex($rootNode);
        } else {
            $this->nodeSortingService->normalizeChildRootNodesSortIndex($project);
        }
    }

    /**
     * Recreates a node branch route.
     *
     * @param \App\Models\Translations\Node $node
     *
     * @throws \Throwable
     *
     * @return \App\Models\Translations\Node
     */
    private function rebuildNodeBranchRoute(Node $node): Node
    {
        /** @var \App\Models\Translations\Node $node */
        $node = $this->nodeRepository->update($node, [
            'route' => $node->isRoot() ? $node->key : implode('.', [$node->parent->route, $node->key]),
        ]);

        $node->descendants->each(function ($item) {
            /* @var Node $item */
            $this->nodeRepository->update($item, [
                'route' => implode('.', [$item->parent->route, $item->key]),
            ]);
        });

        return $node->fresh();
    }
}
