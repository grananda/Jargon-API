<?php

namespace App\Services\GitHub;

use App\Models\Translations\GitConfig;

class GitHubCollaboratorService extends GitHubService
{
    public function getRepositoryCollaborators(GitConfig $gitConfig)
    {
        return $this->gitHubManager
            ->repository()
            ->collaborators()
            ->all($gitConfig->username, $gitConfig->repository)
        ;
    }
}
