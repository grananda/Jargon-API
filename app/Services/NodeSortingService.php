<?php

namespace App\Services;

use App\Models\Translations\Node;
use App\Models\Translations\Project;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class NodeSortingService.
 *
 * @package App\Services\Node
 */
class NodeSortingService
{
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
