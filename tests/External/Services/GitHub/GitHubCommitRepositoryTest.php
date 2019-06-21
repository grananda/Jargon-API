<?php

namespace Tests\External\Services\GitHub;

use App\Models\Translations\GitConfig;
use App\Services\GitHub\GitHubBranchService;
use App\Services\GitHub\GitHubCommitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group  external
 * @covers \App\Services\GitHub\GitHubCommitService
 */
class GitHubCommitRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Models\Translations\Project
     */
    private $project;

    /**
     * @var \App\Services\GitHub\GitHubBranchService
     */
    private $gitHubBranchService;

    /**
     * @var \App\Services\GitHub\GitHubCommitService
     */
    private $gitHubCommitService;

    /**
     * @var \App\Models\Translations\GitConfig
     */
    private $gitConfig;

    public function setUp(): void
    {
        parent::setUp();

        $this->gitConfig = factory(GitConfig::class)->create(
            [
                'access_token' => env('GIT_HUB_TEST_TOKEN'),
                'username'     => env('GIT_HUB_TEST_USER'),
                'repository'   => env('GIT_HUB_TEST_REPO'),
            ]
        );

        $this->gitHubBranchService = resolve(GitHubBranchService::class);

        $this->gitHubCommitService = resolve(GitHubCommitService::class);
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function a_new_file_can_be_committed_into_a_branch()
    {
        // Given
        $branchName = $this->faker->uuid;

        $branch    = $this->gitHubBranchService->createBranch($this->gitConfig, $branchName);
        $branchSha = $branch['object']['sha'];

        // When
        $response = $this->gitHubCommitService->commitFiles($this->gitConfig,
            [
                'branch' => $branchName,
                'sha'    => $branchSha,
                'files'  => [
                    [
                        'path'    => 'test1.php',
                        'mode'    => '100644',
                        'type'    => 'blob',
                        'content' => '<?php [];',
                    ],
                    [
                        'path'    => 'test2.php',
                        'mode'    => '100644',
                        'type'    => 'blob',
                        'content' => '<?php [];',
                    ],
                    [
                        'path'    => 'test3.php',
                        'mode'    => '100644',
                        'type'    => 'blob',
                        'content' => '<?php [];',
                    ],
                ],
            ]
        );

        // Then
        $this->assertSame("refs/heads/{$branchName}", $response['ref']);
        $this->assertSame('commit', $response['object']['type']);

        // Clean
        $this->gitHubBranchService->removeBranch($this->gitConfig, $branchName);
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function an_existing_file_can_be_committed_into_a_branch()
    {
        // Given
        $branchName = $this->faker->uuid;

        $branch    = $this->gitHubBranchService->createBranch($this->gitConfig, $branchName);
        $branchSha = $branch['object']['sha'];

        // When
        $this->gitHubCommitService->commitFiles($this->gitConfig,
            [
                'branch' => $branchName,
                'sha'    => $branchSha,
                'files'  => [
                    [
                        'path'    => 'test1.php',
                        'mode'    => '100644',
                        'type'    => 'blob',
                        'content' => '<?php [];',
                    ],
                ],
            ]
        );

        $response = $this->gitHubCommitService->commitFiles($this->gitConfig,
            [
                'branch' => $branchName,
                'sha'    => $branchSha,
                'files'  => [
                    [
                        'path'    => 'test1.php',
                        'mode'    => '100644',
                        'type'    => 'blob',
                        'content' => '<?php [1];',
                    ],
                ],
            ]
        );

        // Then
        $this->assertSame("refs/heads/{$branchName}", $response['ref']);
        $this->assertSame('commit', $response['object']['type']);

        // Clean
        $this->gitHubBranchService->removeBranch($this->gitConfig, $branchName);
    }
}
