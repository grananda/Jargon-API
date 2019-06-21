<?php

namespace App\Services\GitHub;

use App\Models\Translations\GitConfig;

class GitHubRepoService extends GitHubService
{
    /**
     * Gets list of repositories available for a given project git configuration.
     *
     * @param \App\Models\Translations\GitConfig $gitConfig
     *
     * @throws \App\Exceptions\GitHubConnectionException
     *
     * @return array
     */
    public function getRepositoryList(GitConfig $gitConfig)
    {
        $this->authenticate($gitConfig);

        return $this->gitHubManager
            ->user()
            ->myRepositories()
        ;
    }

    /**
     * Retrieves a repository details for a given project git configuration.
     *
     * @param \App\Models\Translations\GitConfig $gitConfig
     * @param string                             $repository
     *
     * @throws \App\Exceptions\GitHubConnectionException
     *
     * @return array
     */
    public function getRepositoryDetails(GitConfig $gitConfig, string $repository)
    {
        $this->authenticate($gitConfig);

        return $this->gitHubManager
            ->repo()
            ->show($gitConfig->username, $repository)
        ;
    }
}
