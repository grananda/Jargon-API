<?php

namespace Tests\External\Services\GitHub;

use App\Models\Translations\GitConfig;
use App\Services\GitHub\GitHubBranchService;
use App\Services\GitHub\GitHubCommitService;
use App\Services\GitHub\GitHubPullRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group  external
 * @covers \App\Services\GitHub\GitHubPullRequestService
 */
class GitHubPullRequestServiceTest extends TestCase
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
     * @var \App\Services\GitHub\GitHubPullRequestService
     */
    private $gitHubPullRequestService;

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

        $this->gitHubPullRequestService = resolve(GitHubPullRequestService::class);
    }

    /** @test */
    public function a_pull_request_is_created()
    {
        // Given
        $branchName = $this->faker->uuid;

        $branch    = $this->gitHubBranchService->createBranch($this->gitConfig, $branchName);
        $branchSha = $branch['object']['sha'];

        $commit = $this->gitHubCommitService->commitFiles($this->gitConfig,
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

        // When
        $response = $this->gitHubPullRequestService->createPullRequest($this->gitConfig, $branchName);

        // Then
        $this->assertArrayHasKey('url', $response);
        $this->assertSame('open', $response['state']);

        // Clean
        $this->gitHubPullRequestService->closePullRequest($this->gitConfig, $response['number']);
        $this->gitHubBranchService->removeBranch($this->gitConfig, $branchName);
    }
}
