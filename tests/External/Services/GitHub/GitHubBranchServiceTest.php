<?php

namespace Tests\External\Services\GitHub;

use App\Models\Translations\GitConfig;
use App\Services\GitHub\GitHubBranchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group  external
 * @covers \App\Services\GitHub\GitHubBranchService
 */
class GitHubBranchServiceTest extends TestCase
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
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function a_new_branch_can_be_created()
    {
        // Given
        $branchName = $this->faker->uuid;
        $ref        = "refs/heads/{$branchName}";

        // When
        $response = $this->gitHubBranchService->createBranch($this->gitConfig, $branchName);

        $delete = $this->gitHubBranchService->removeBranch($this->gitConfig, $branchName);

        // Then
        $this->assertIsArray($response);
        $this->assertSame($ref, $response['ref']);
        $this->assertEmpty($delete);
    }
}
