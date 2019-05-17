<?php

namespace App\Services\Traits;

use App\Models\Translations\Node;
use App\Models\Translations\Project;
use App\Repositories\NodeRepository;
use Illuminate\Database\Eloquent\Collection;

trait NodeToolsTrait
{
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
     * Sets children nodes sort_index to fit the natural nodes sequence by id.
     *
     * @param \App\Models\Translations\Node $parentNode
     */
    public function normalizeChildNodesSortIndex(Node $parentNode): void
    {
        $siblings         = $parentNode->children;
        $siblingsSequence = array_column($siblings->toArray(), 'id');

        $this->matchNodesSortIndexToSequence($siblings, $siblingsSequence);
    }

    /**
     * Sets project root nodes sort_index to fir the natural nodes sequence by it.
     *
     * @param Project $project
     */
    public function normalizeChildRootNodesSortIndex(Project $project): void
    {
        $siblings         = $project->rootNodes()->get();
        $siblingsSequence = array_column($siblings->toArray(), 'id');

        $this->matchNodesSortIndexToSequence($siblings, $siblingsSequence);
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
    public function rebuildNodeBranchRoute(Node $node): Node
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

    /**
     * Rewrites sibling nodes sort index to provided node sequence.
     *
     * @param \Illuminate\Database\Eloquent\Collection $siblings
     * @param array                                    $siblingsSequence
     */
    private function matchNodesSortIndexToSequence(Collection $siblings, array $siblingsSequence)
    {
        /* @var \App\Models\Translations\Node $node */
        $siblings->each(function ($node) use ($siblingsSequence) {
            /* @var \App\Models\Translations\Node $node */
            $node->update(['sort_index' => array_search($node->id, $siblingsSequence)]);
        });
    }
}
