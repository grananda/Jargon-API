<?php

namespace App\Repositories\GitHub;

use App\Exceptions\GitHubConnectionException;
use App\Models\Translations\Project;
use Github\Client;
use GrahamCampbell\GitHub\GitHubManager;

class GitHubRepository
{
    /**
     * @var \GrahamCampbell\GitHub\GitHubManager
     */
    private $gitHubManager;

    /**
     * @var string
     */
    private $authType;

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
     * @param \App\Models\Translations\Project $project
     *
     * @throws \App\Exceptions\GitHubConnectionException
     *
     * @return array
     */
    public function getRepositoryList(Project $project)
    {
        if (! $project->hasGitHubAccess()) {
            throw new GitHubConnectionException(trans('Project GitHub configuration missing'));
        }

        $this->gitHubManager->authenticate($project->gitHubConfig->access_token, $this->authType);

        return $this->gitHubManager->user()->myRepositories();
    }
}
