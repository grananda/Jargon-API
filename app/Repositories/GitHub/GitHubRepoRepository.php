<?php

namespace App\Repositories\GitHub;

use App\Models\Translations\Project;

class GitHubRepoRepository extends GitHubRepository
{
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
}
