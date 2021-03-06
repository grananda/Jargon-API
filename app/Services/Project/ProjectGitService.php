<?php

namespace App\Services\Project;

use App\Models\Translations\Project;
use App\Repositories\GitFileHashRepository;
use App\Services\GitHub\GitHubAssigneeService;
use App\Services\GitHub\GitHubBranchService;
use App\Services\GitHub\GitHubCommitService;
use App\Services\GitHub\GitHubPullRequestService;
use App\Services\GitHub\GutHubReviewService;

class ProjectGitService
{
    /**
     * @var \App\Services\Project\ProjectTranslationParserService
     */
    private $projectTranslationParserService;

    /**
     * @var \App\Services\GitHub\GutHubReviewService
     */
    private $gitHubReviewService;

    /**
     * @var \App\Services\GitHub\GitHubAssigneeService
     */
    private $gitHubAssigneeService;

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
     * @var \App\Repositories\GitFileHashRepository
     */
    private $gitFileHashRepository;

    /**
     * ProjectGitService constructor.
     *
     * @param \App\Services\Project\ProjectTranslationParserService $projectTranslationParserService
     * @param \App\Services\GitHub\GitHubPullRequestService         $gitHubPullRequestService
     * @param \App\Services\GitHub\GitHubCommitService              $gitHubCommitService
     * @param \App\Services\GitHub\GitHubBranchService              $gitHunBranchService
     * @param \App\Services\GitHub\GitHubAssigneeService            $gitHubIssueService
     * @param \App\Services\GitHub\GutHubReviewService              $gitHubReviewService
     * @param \App\Repositories\GitFileHashRepository               $gitFileHashRepository
     */
    public function __construct(
        ProjectTranslationParserService $projectTranslationParserService,
        GitHubPullRequestService $gitHubPullRequestService,
        GitHubCommitService $gitHubCommitService,
        GitHubBranchService $gitHunBranchService,
        GitHubAssigneeService $gitHubIssueService,
        GutHubReviewService $gitHubReviewService,
        GitFileHashRepository $gitFileHashRepository
    ) {
        $this->projectTranslationParserService = $projectTranslationParserService;
        $this->gitHubPullRequestService        = $gitHubPullRequestService;
        $this->gitHubCommitService             = $gitHubCommitService;
        $this->gitHunBranchService             = $gitHunBranchService;
        $this->gitHubAssigneeService           = $gitHubIssueService;
        $this->gitHubReviewService             = $gitHubReviewService;
        $this->gitFileHashRepository           = $gitFileHashRepository;
    }

    /**
     * Creates a project file array from project nodes and generates a pull request into target branch.
     *
     * @param \App\Models\Translations\Project $project
     *
     * @throws \App\Exceptions\GitHubConnectionException
     * @throws \Github\Exception\MissingArgumentException
     * @throws \Throwable
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
                /** @var \App\Models\Translations\GitFileHash $lastPullRequest */
                $lastPullRequest = $this->gitFileHashRepository->findLastPullRequestByProject($project) ?? $this->gitFileHashRepository->getModel();

                /** @var \App\Models\Translations\GitFileHash $lastHash */
                $lastHash = $this->gitFileHashRepository->findLastByProjectAndFile($project, $file) ?? $this->gitFileHashRepository->getModel();

                if (! $lastHash->pull_request_number || $lastPullRequest->pull_request_number !== $lastHash->pull_request_number) {
                    $commitFiles[] = [
                        'path'    => "{$file['path']}/{$file['file']}",
                        'mode'    => GitHubCommitService::GIT_HUB_COMMIT_FILE_MODE,
                        'type'    => GitHubCommitService::GIT_HUB_COMMIT_FILE_TYPE,
                        'content' => $file['content'],
                    ];
                }
            }
        }

        if (! sizeof($commitFiles)) {
            return [];
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

        $assignee = $this->gitHubAssigneeService->assignUserToPullRequest($project->gitConfig, $pullRequest['number']);

        $reviewer = $this->gitHubReviewService->setReviewerToPullRequest($project->gitConfig, $pullRequest['number']);

        foreach ($files as $fileSet) {
            foreach ($fileSet as $file) {
                $this->gitFileHashRepository->create([
                    'locale'              => $file['locale'],
                    'file'                => $file['file'],
                    'hash'                => $file['hash'],
                    'pull_request_number' => $pullRequest['number'],
                    'project_id'          => $project->id,
                ]);
            }
        }

        return [
            'branch'              => $branchName,
            'pull_request_number' => $pullRequest['number'],
            'assignee'            => $assignee,
            'reviewer'            => $reviewer,
        ];
    }
}
