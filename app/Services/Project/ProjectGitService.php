<?php

namespace App\Services\Project;

use App\Models\Translations\Project;
use App\Services\GitHub\GitHubBranchService;
use App\Services\GitHub\GitHubCommitService;
use App\Services\GitHub\GitHubPullRequestService;

class ProjectGitService
{
    /**
     * @var \App\Services\Project\ProjectTranslationParserService
     */
    private $projectTranslationParserService;

    /**
     * @var \App\Services\GitHub\GitHubPullRequestService
     */
    private $gitHubPullRequestService;

    /**
     * @var \App\Services\GitHub\GitHubCommitService
     */
    private $gitHubCommitService;

    /**
     * @var \App\Services\GitHub\GitHubBranchService
     */
    private $gitHunBranchService;

    /**
     * ProjectGitService constructor.
     *
     * @param \App\Services\Project\ProjectTranslationParserService $projectTranslationParserService
     * @param \App\Services\GitHub\GitHubPullRequestService         $gitHubPullRequestService
     * @param \App\Services\GitHub\GitHubCommitService              $gitHubCommitService
     * @param \App\Services\GitHub\GitHubBranchService              $gitHunBranchService
     */
    public function __construct(
        ProjectTranslationParserService $projectTranslationParserService,
        GitHubPullRequestService $gitHubPullRequestService,
        GitHubCommitService $gitHubCommitService,
        GitHubBranchService $gitHunBranchService
    ) {
        $this->projectTranslationParserService = $projectTranslationParserService;
        $this->gitHubPullRequestService        = $gitHubPullRequestService;
        $this->gitHubCommitService             = $gitHubCommitService;
        $this->gitHunBranchService             = $gitHunBranchService;
    }

    /**
     * Creates a project file array from project nodes and generates a pull request into target branch.
     *
     * @param \App\Models\Translations\Project $project
     *
     * @throws \App\Exceptions\GitHubConnectionException
     * @throws \Github\Exception\MissingArgumentException
     *
     * @return array
     */
    public function createPullRequestFromProjectNodes(Project $project): array
    {
        /** @var string $branchName */
        $branchName = uniqid();

        /** @var array $files */
        $files = $this->projectTranslationParserService->parseProjectTranslationTree($project);

        /** @var array $commitFiles */
        $commitFiles = [];

        foreach ($files as $fileSet) {
            foreach ($fileSet as $file) {
                $commitFiles[] = [
                    'path'    => "{$file['path']}/{$file['file']}",
                    'mode'    => GitHubCommitService::GIT_HUB_COMMIT_FILE_MODE,
                    'type'    => GitHubCommitService::GIT_HUB_COMMIT_FILE_TYPE,
                    'content' => $file['content'],
                ];
            }
        }

        $branch = $this->gitHunBranchService->createBranch($project->gitConfig, $branchName);

        $this->gitHubCommitService->commitFiles($project->gitConfig,
            [
                'branch' => $branchName,
                'sha'    => $branch['object']['sha'],
                'files'  => $commitFiles,
            ]
        );

        $pullRequest = $this->gitHubPullRequestService->createPullRequest($project->gitConfig, $branchName);

        return [
            'branch' => $branchName,
            'number' => $pullRequest['number'],
        ];
    }
}
