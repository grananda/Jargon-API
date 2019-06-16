<?php

namespace App\Repositories\GitHub;

use App\Exceptions\GitHubConnectionException;
use App\Models\Translations\Project;
use Github\Client;
use GrahamCampbell\GitHub\GitHubManager;

class GitHubRepository
{
    /**
     * The GitHubManager instance.
     *
     * @var \GrahamCampbell\GitHub\GitHubManager
     */
    private $gitHubManager;

    /**
     * Authorization type for GitHub API.
     *
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
     * Gets list of repositories available for a given project git configuration.
     *
     * @param \App\Models\Translations\Project $project
     *
     * @throws \App\Exceptions\GitHubConnectionException
     *
     * @return array
     */
    public function getRepositoryList(Project $project)
    {
        $this->authenticate($project);

        return $this->gitHubManager
            ->user()
            ->myRepositories()
        ;
    }

    /**
     * Retrieves a repository details for a given project git configuration.
     *
     * @param \App\Models\Translations\Project $project
     * @param string                           $branch
     *
     * @throws \App\Exceptions\GitHubConnectionException
     *
     * @return array
     */
    public function getRepositoryDetails(Project $project, string $branch)
    {
        $this->authenticate($project);

        return $this->gitHubManager
            ->repo()
            ->show($project->gitHubConfig->username, $branch)
        ;
    }

    /**
     * Creates a branch within the current project working repository,.
     *
     * @param \App\Models\Translations\Project $project
     * @param string                           $branch
     *
     * @throws \Github\Exception\MissingArgumentException
     * @throws \App\Exceptions\GitHubConnectionException
     *
     * @return array
     */
    public function createBranch(Project $project, string $branch)
    {
        $this->authenticate($project);

        /** @var array $reference */
        $reference = $this->getBaseBranchReferenceDetails($project);

        return $this->gitHubManager
            ->gitData()
            ->references()
            ->create($project->gitHubConfig->username, $project->gitHubConfig->repository,
                [
                    'ref' => "refs/heads/{$branch}",
                    'sha' => $reference['object']['sha'],
                ]
            )
        ;
    }

    /**
     * Removes a branch within the current project working repository,.
     *
     * @param \App\Models\Translations\Project $project
     * @param string                           $branch
     *
     * @throws \App\Exceptions\GitHubConnectionException
     *
     * @return array
     */
    public function removeBranch(Project $project, string $branch)
    {
        $this->authenticate($project);

        return $this->gitHubManager
            ->gitData()
            ->references()
            ->remove($project->gitHubConfig->username, $project->gitHubConfig->repository, "heads/{$branch}")
        ;
    }

    /**
     * Gets project base branch reference data.
     *
     * @param \App\Models\Translations\Project $project
     *
     * @throws \App\Exceptions\GitHubConnectionException
     *
     * @return array
     */
    private function getBaseBranchReferenceDetails(Project $project)
    {
        $this->authenticate($project);

        return $this->gitHubManager
            ->gitData()
            ->references()
            ->show(
                $project->gitHubConfig->username,
                $project->gitHubConfig->repository,
                "heads/{$project->gitHubConfig->base_branch}"
            )
        ;
    }

    /**
     * Authenticates user in GitHub API.
     *
     * @param \App\Models\Translations\Project $project
     *
     * @throws \App\Exceptions\GitHubConnectionException
     */
    private function authenticate(Project $project): void
    {
        if (! $project->hasGitHubAccess()) {
            throw new GitHubConnectionException(trans('Project GitHub configuration missing'));
        }

        $this->gitHubManager
            ->authenticate($project->gitHubConfig->access_token, Client::AUTH_HTTP_TOKEN)
        ;
    }
}
