<?php

namespace App\Jobs;

use App\Models\Translations\Project;
use App\Services\Project\ProjectTranslationParserService;

class CreateProjectPullRequest extends AbstractJob
{
    /**
     * @var \App\Models\Translations\Project
     */
    private $project;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Translations\Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Project\ProjectTranslationParserService $projectTranslationParserService
     *
     * @return void
     */
    public function handle(ProjectTranslationParserService $projectTranslationParserService)
    {
        $projectTranslationParserService->parseProjectTranslationTree($this->project);
    }
}
