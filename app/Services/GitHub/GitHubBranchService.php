<?php

namespace App\Services\GitHub;

use App\Models\Translations\GitConfig;

class GitHubBranchService extends GitHubService
{
    /**
     * Creates a branch within the current project working repository,.
     *
     * @param \App\Models\Translations\GitConfig $gitConfig
     * @param string                             $branch
     *
     * @throws \App\Exceptions\GitHubConnectionException
     * @throws \Github\Exception\MissingArgumentException
     *
     * @return array
     */
    public function createBranch(GitConfig $gitConfig, string $branch)
    {
        $this->authenticate($gitConfig);

        /** @var array $reference */
        $reference = $this->getBaseBranchReferenceDetails($gitConfig);

        return $this->gitHubManager
            ->gitData()
            ->references()
            ->create($gitConfig->username, $gitConfig->repository,
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
     * @param \App\Models\Translations\GitConfig $gitConfig
     * @param string                             $branch
     *
     * @throws \App\Exceptions\GitHubConnectionException
     *
     * @return array
     */
    public function removeBranch(GitConfig $gitConfig, string $branch)
    {
        $this->authenticate($gitConfig);

        return $this->gitHubManager
            ->gitData()
            ->references()
            ->remove($gitConfig->username, $gitConfig->repository, "heads/{$branch}")
        ;
    }

    /**
     * Gets project base branch reference data.
     *
     * @param \App\Models\Translations\GitConfig $gitConfig
     *
     * @throws \App\Exceptions\GitHubConnectionException
     *
     * @return array
     */
    private function getBaseBranchReferenceDetails(GitConfig $gitConfig)
    {
        return $this->getReferenceDetails($gitConfig, $gitConfig->base_branch);
    }
}
