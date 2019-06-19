<?php

namespace App\Repositories\GitHub;

use Carbon\Carbon;

class GitHubCommitRepository extends GitHubRepository
{
    /**
     * Commits a set of file2.
     * http://www.levibotelho.com/development/commit-a-file-with-the-github-api/.
     *
     * @param array $params
     *
     * @throws \Github\Exception\MissingArgumentException
     *
     * @return array
     */
    public function commitFiles(array $params)
    {
        // Step 2
        $head        = $this->getCommit($params['username'], $params['repository'], $params['sha']);
        $parentSha   = $head['sha'];
        $baseTreeSha = $head['tree']['sha'];

        // Step 3
        //		$blob    = $this->createBlob($params['username'], $params['repository'], 'this is a test');
        //		$blobSha = $blob['sha'];

        // Step 5
        $tree    = $this->createTree($params['username'], $params['repository'], $baseTreeSha, $params['files']);
        $treeSha = $tree['sha'];

        // Step 6
        $commit    = $this->createCommit($params['username'], $params['repository'], $params['email'], $parentSha, $treeSha);
        $commitSha = $commit['sha'];

        return $this->updateReference($params['username'], $params['repository'], $commitSha, $params['branch']);
    }

    /**
     * Get branch head commit.
     *
     * @param string $username
     * @param string $repository
     * @param string $sha
     *
     * @return array
     */
    private function getCommit(string $username, string $repository, string $sha)
    {
        return $this->gitHubManager
            ->gitData()
            ->commits()
            ->show($username, $repository, $sha)
        ;
    }

    /**
     * Creates blob with file content.
     *
     * @param string $username
     * @param string $repository
     * @param string $content
     *
     * @throws \Github\Exception\MissingArgumentException
     *
     * @return array
     */
    private function createBlob(string $username, string $repository, string $content)
    {
        return $this->gitHubManager
            ->gitData()
            ->blobs()
            ->create($username, $repository,
                [
                    'content'  => $content,
                    'encoding' => 'utf-8',
                ]
            )
        ;
    }

    /**
     * Creates tree for files to reside.
     *
     * @param string $username
     * @param string $repository
     * @param string $treeSha
     * @param array  $files
     *
     * @throws \Github\Exception\MissingArgumentException
     *
     * @return array
     */
    private function createTree(string $username, string $repository, string $treeSha, array $files)
    {
        return $this->gitHubManager
            ->gitData()
            ->trees()
            ->create($username, $repository,
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
     * @param string $username
     * @param string $repository
     * @param string $email
     * @param string $parentSha
     * @param string $treeSha
     *
     * @throws \Github\Exception\MissingArgumentException
     *
     * @return array
     */
    private function createCommit(string $username, string $repository, string $email, string $parentSha, string $treeSha)
    {
        return $this->gitHubManager
            ->gitData()
            ->commits()
            ->create($username, $repository,
                [
                    'message' => 'Jargon automatic translation sync 1234-5678-9012-3456',
                    'author'  => [
                        'name'  => $username,
                        'email' => $email,
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
     * @param string $username
     * @param string $repository
     * @param string $commitSha
     * @param string $branchName
     *
     * @throws \Github\Exception\MissingArgumentException
     *
     * @return array
     */
    private function updateReference(string $username, string $repository, string $commitSha, string $branchName)
    {
        return $this->gitHubManager
            ->gitData()
            ->references()
            ->update($username, $repository, "heads/{$branchName}",
                [
                    'sha'   => $commitSha,
                    'force' => true,
                ]
            )
        ;
    }
}
