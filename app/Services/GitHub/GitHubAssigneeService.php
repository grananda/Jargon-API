<?php

namespace App\Services\GitHub;

use App\Models\Translations\GitConfig;

class GitHubAssigneeService extends GitHubService
{
    /**
     * Assigns current user to pull request issue.
     *
     * @param \App\Models\Translations\GitConfig $gitConfig
     * @param string                             $pullRequestNumber
     *
     * @throws \Github\Exception\MissingArgumentException
     *
     * @return string
     */
    public function assignUserToPullRequest(GitConfig $gitConfig, string $pullRequestNumber)
    {
        return $this->gitHubManager
            ->issues()
            ->assignees()
            ->add($gitConfig->username, $gitConfig->repository, $pullRequestNumber,
                [
                    'assignees' => [
                            $gitConfig->username,
                        ],
                ]
            )
        ;
    }
}
