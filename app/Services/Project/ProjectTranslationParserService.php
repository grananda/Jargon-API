<?php

namespace App\Services\Project;

use App\Models\Translations\Project;
use App\Services\Node\NodeTranslationParserService;

class ProjectTranslationParserService
{
    /**
     * @var \App\Services\Node\NodeTranslationParserService
     */
    private $nodeTranslationParserService;

    /**
     * ProjectTranslationParserService constructor.
     *
     * @param \App\Services\Node\NodeTranslationParserService $nodeTranslationParserService
     */
    public function __construct(NodeTranslationParserService $nodeTranslationParserService)
    {
        $this->nodeTranslationParserService = $nodeTranslationParserService;
    }

    /**
     * @param \App\Models\Translations\Project $project
     *
     * @return array
     */
    public function parseProjectTranslationTree(Project $project): array
    {
        /** @var array $arr */
        $arr = [];

        /** @var \App\Models\Dialect $defaultDialect */
        $defaultDialect = $project->dialects()->where('is_default', true)->first();

        foreach ($project->rootNodes as $rootNode) {
            /** @var array $files */
            $files = [];

            foreach ($project->dialects as $dialect) {
                $files[] = $this->nodeTranslationParserService->parseTranslationFile($rootNode, $dialect, $defaultDialect, $project->jargonOptions);
            }

            $arr[] = $files;
        }

        return $arr;
    }
}
