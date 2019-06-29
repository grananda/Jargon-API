<?php

namespace App\Repositories;

use App\Models\Translations\Node;
use App\Models\Translations\Project;
use Illuminate\Database\Connection;

/**
 * Class NodeRepository.
 *
 * @package App\Repositories
 */
class NodeRepository extends CoreRepository
{
    /**
     * Node Repository constructor.
     *
     * @param \Illuminate\Database\Connection $dbConnection
     * @param \App\Models\Translations\Node   $model
     */
    public function __construct(Connection $dbConnection, Node $model)
    {
        parent::__construct($dbConnection, $model);
    }

    /**
     * Stores a new node.
     *
     * @param \App\Models\Translations\Project $project
     * @param \App\Models\Translations\Node    $parentNode
     * @param array                            $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function createNode(Project $project, ?Node $parentNode, array $attributes = [])
    {
        return $this->dbConnection->transaction(function () use ($project, $parentNode, $attributes) {
            $attributes['key'] = $attributes['key'] ?? __(Node::TEMPLATE_KEY, ['count' => $parentNode ? $parentNode->children->count() : 0 + 1]);
            $attributes['project_id'] = $project->id;
            $attributes['sort_index'] = $parentNode ? Node::NEW_NODE_INDEX : 1;
            $attributes['route'] = $parentNode ? implode('.', [$parentNode->route, $attributes['key']]) : $attributes['key'];

            /* @var \App\Models\Translations\Node $entity */
            return Node::create($attributes, $parentNode);
        });
    }
}
