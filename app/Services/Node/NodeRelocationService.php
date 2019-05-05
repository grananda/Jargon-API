<?php

namespace App\Services\Node;

use App\Models\Translations\Node;
use App\Services\Traits\NodeToolsTrait;

class NodeRelocationService
{
    use NodeToolsTrait;

    public function relocateNode(Node $node, Node $parent): Node
    {
        return $parent->fresh();
    }
}
