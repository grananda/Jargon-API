<?php

namespace App\Services\GitHub;

use App\Models\Translations\GitConfig;
use Carbon\Carbon;

class GitHubCommitService extends GitHubService
{
    public const GIT_HUB_COMMIT_FILE_MODE = '100644';

    public const GIT_HUB_COMMIT_FILE_TYPE = 'blob';

    private const STANDARD_COMMIT_MESSAGE_PREFIX = 'Jargon translation sync ';

    /**
     * Commits a set of file2.
     * http://www.levibotelho.com/development/commit-a-file-with-the-github-api/.
     *
     * @param \App\Models\Translations\GitConfig $gitConfig
     * @param array                              $params
     *
     * @throws \Github\Exception\MissingArgumentException
     *
     * @return array
     */
    public function commitFiles(GitConfig $gitConfig, array $params): array
    {
        // Step 2 - Obtain reference to head by requesting last commit.
        $head        = $this->getCommit($gitConfig, $params['sha']);
        $parentSha   = $head['sha'];
        $baseTreeSha = $head['tree']['sha'];

        // Step 5 - Create tree where the files will reside.
        $tree    = $this->createTree($gitConfig, $baseTreeSha, $params['files']);
        $treeSha = $tree['sha'];

        // Step 6
        $commit    = $this->createCommit($gitConfig, $parentSha, $treeSha);
        $commitSha = $commit['sha'];

        return $this->updateReference($gitConfig, $commitSha, $params['branch']);
    }

    /**
     * Get branch head commit.
     *
     * @param \App\Models\Translations\GitConfig $gitConfig
     * @param string                             $sha
     *
     * @return array
     */
    private function getCommit(GitConfig $gitConfig, string $sha): array
    {
        return $this->gitHubManager
            ->gitData()
            ->commits()
            ->show($gitConfig->username, $gitConfig->repository, $sha)
        ;
    }

    /**
     * Creates tree for files to reside.
     *
     * @param \App\Models\Translations\GitConfig $gitConfig
     * @param string                             $treeSha
     * @param array                              $files
     *
     * @throws \Github\Exception\MissingArgumentException
     *
     * @return array
     */
    private function createTree(GitConfig $gitConfig, string $treeSha, array $files): array
    {
        return $this->gitHubManager
            ->gitData()
            ->trees()
            ->create($gitConfig->username, $gitConfig->repository,
                [
                    'base_tree' => $treeSha,
                    'tree'      => $files,
                ]
            )
        ;
    }

    /**
     * Creates commit.
     *
     * @param \App\Models\Translations\GitConfig $gitConfig
     * @param string                             $parentSha
     * @param string                             $treeSha
     *
     * @throws \Github\Exception\MissingArgumentException
     *
     * @return array
     */
    private function createCommit(GitConfig $gitConfig, string $parentSha, string $treeSha): array
    {
        return $this->gitHubManager
            ->gitData()
            ->commits()
            ->create($gitConfig->username, $gitConfig->repository,
                [
                    'message' => implode(' ', [self::STANDARD_COMMIT_MESSAGE_PREFIX, uniqid()]),
                    'author'  => [
                        'name'  => $gitConfig->username,
                        'email' => $gitConfig->email,
                        'date'  => Carbon::now(),
                    ],
                    'parents' => [$parentSha],
                    'tree'    => $treeSha,
                ]
            )
        ;
    }

    /**
     * Updates brach reference head.
     *
     * @param \App\Models\Translations\GitConfig $gitConfig
     * @param string                             $commitSha
     * @param string                             $branchName
     *
     * @throws \Github\Exception\MissingArgumentException
     *
     * @return array
     */
    private function updateReference(GitConfig $gitConfig, string $commitSha, string $branchName): array
    {
        return $this->gitHubManager
            ->gitData()
            ->references()
            ->update($gitConfig->username, $gitConfig->repository, "heads/{$branchName}",
                [
                    'sha'   => $commitSha,
                    'force' => true,
                ]
            )
        ;
    }
}
