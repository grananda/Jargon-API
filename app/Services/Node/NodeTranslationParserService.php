<?php

namespace App\Services\Node;

use App\Models\Dialect;
use App\Models\Translations\Node;

class NodeTranslationParserService
{
    /**
     * @param \App\Models\Translations\Node $rootNode
     * @param \App\Models\Dialect           $dialect
     *
     * @return array
     */
    public function parseTranslationFile(Node $rootNode, Dialect $dialect): array
    {
        return [];
    }
}
