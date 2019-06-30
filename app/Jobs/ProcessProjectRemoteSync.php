<?php

namespace App\Jobs;

use App\Models\Translations\Project;
use App\Services\Node\NodeTranslationParserService;

class ProcessProjectRemoteSync extends AbstractJob
{
    /**
     * @var \App\Models\Translations\Project
     */
    private $project;

    /**
     * @var string
     */
    private $json;

    /**
     * ProcessProjectRemoteSync constructor.
     *
     * @param \App\Models\Translations\Project $project
     * @param string                           $json
     */
    public function __construct(Project $project, string $json)
    {
        $this->project = $project;
        $this->json    = $json;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Node\NodeTranslationParserService $nodeTranslationParserService
     *
     * @return void
     */
    public function handle(NodeTranslationParserService $nodeTranslationParserService)
    {
        $nodeTranslationParserService->jsonToTree($this->project, $this->json);
    }
}
