<?php

namespace App\Services\GitHub;

use App\Models\Translations\GitConfig;
use GrahamCampbell\GitHub\GitHubManager;

class GutHubReviewService extends GitHubService
{
    /**
     * @var \App\Services\GitHub\GitHubCollaboratorService
     */
    private $gitCollaboratorService;

    /**
     * GutHubReviewService constructor.
     *
     * @param \GrahamCampbell\GitHub\GitHubManager           $gitHubManager
     * @param \App\Services\GitHub\GitHubCollaboratorService $gitCollaboratorService
     */
    public function __construct(GitHubManager $gitHubManager, GitHubCollaboratorService $gitCollaboratorService)
    {
        parent::__construct($gitHubManager);

        $this->gitCollaboratorService = $gitCollaboratorService;
    }

    /**
     * Assign a reviewer to a pull request.
     *
     * @param \App\Models\Translations\GitConfig $gitConfig
     * @param string                             $pullRequestNumber
     *
     * @return string
     */
    public function setReviewerToPullRequest(GitConfig $gitConfig, string $pullRequestNumber)
    {
        /** @var array $reviewers */
        $collaborators = $this->gitCollaboratorService->getRepositoryCollaborators($gitConfig);

        if (sizeof($collaborators) == 0) {
            return null;
        }

        $_collaborators = collect($collaborators)->filter(function ($item) use ($gitConfig) {
            return $item['login'] !== $gitConfig->username;
        })->pluck('login')->toArray();

        shuffle($_collaborators);

        $this->gitHubManager
            ->pullRequests()
            ->reviewRequests()
            ->create($gitConfig->username, $gitConfig->repository, $pullRequestNumber,
                [
                    current($_collaborators),
                ]
            )
        ;

        return $_collaborators;
    }
}
