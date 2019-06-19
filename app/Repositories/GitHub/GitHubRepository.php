<?php

namespace App\Repositories\GitHub;

use App\Exceptions\GitHubConnectionException;
use App\Models\Translations\Project;
use Github\Client;
use GrahamCampbell\GitHub\GitHubManager;

abstract class GitHubRepository
{
    /**
     * The GitHubManager instance.
     *
     * @var \GrahamCampbell\GitHub\GitHubManager
     */
    protected $gitHubManager;

    /**
     * Authorization type for GitHub API.
     *
     * @var string
     */
    protected $authType;

    /**
     * GitHubRepository constructor.
     *
     * @param \GrahamCampbell\GitHub\GitHubManager $gitHubManager
     */
    public function __construct(GitHubManager $gitHubManager)
    {
        $this->gitHubManager = $gitHubManager;

        $this->authType = Client::AUTH_HTTP_TOKEN;
    }

    /**
     * Authenticates user in GitHub API.
     *
     * @param \App\Models\Translations\Project $project
     *
     * @throws \App\Exceptions\GitHubConnectionException
     */
    protected function authenticate(Project $project): void
    {
        if (! $project->hasGitHubAccess()) {
            throw new GitHubConnectionException(trans('Project GitHub configuration missing'));
        }

        $this->gitHubManager
            ->authenticate($project->gitHubConfig->access_token, Client::AUTH_HTTP_TOKEN)
        ;
    }

    /**
     * Gets project base branch reference data.
     *
     * @param \App\Models\Translations\Project $project
     * @param string                           $branch
     *
     * @throws \App\Exceptions\GitHubConnectionException
     *
     * @return array
     */
    protected function getReferenceDetails(Project $project, string $branch)
    {
        $this->authenticate($project);

        return $this->gitHubManager
            ->gitData()
            ->references()
            ->show(
                $project->gitHubConfig->username,
                $project->gitHubConfig->repository,
                "heads/{$branch}"
            )
        ;
    }
}
