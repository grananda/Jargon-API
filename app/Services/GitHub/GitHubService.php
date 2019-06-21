<?php

namespace App\Services\GitHub;

use App\Exceptions\GitHubConnectionException;
use App\Models\Translations\GitConfig;
use Github\Client;
use GrahamCampbell\GitHub\GitHubManager;

abstract class GitHubService
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
     * @param \App\Models\Translations\GitConfig $gitConfig
     *
     * @throws \App\Exceptions\GitHubConnectionException
     */
    protected function authenticate(GitConfig $gitConfig): void
    {
        if (! $gitConfig->access_token) {
            throw new GitHubConnectionException(trans('Project GitHub configuration missing'));
        }

        $this->gitHubManager
            ->authenticate($gitConfig->access_token, Client::AUTH_HTTP_TOKEN)
        ;
    }

    /**
     * Gets project base branch reference data.
     *
     * @param \App\Models\Translations\GitConfig $gitConfig
     * @param string                             $branch
     *
     * @throws \App\Exceptions\GitHubConnectionException
     *
     * @return array
     */
    protected function getReferenceDetails(GitConfig $gitConfig, string $branch)
    {
        $this->authenticate($gitConfig);

        return $this->gitHubManager
            ->gitData()
            ->references()
            ->show(
                $gitConfig->username,
                $gitConfig->repository,
                "heads/{$branch}"
            )
        ;
    }
}
