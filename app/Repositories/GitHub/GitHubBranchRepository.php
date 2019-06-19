<?php

namespace App\Repositories\GitHub;

use App\Models\Translations\Project;

class GitHubBranchRepository extends GitHubRepository
{
    /**
     * Creates a branch within the current project working repository,.
     *
     * @param \App\Models\Translations\Project $project
     * @param string                           $branch
     *
     * @throws \App\Exceptions\GitHubConnectionException
     * @throws \Github\Exception\MissingArgumentException
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
        return $this->getReferenceDetails($project, $project->gitHubConfig->base_branch);
    }
}
