<?php

namespace Tests\External\Repositories\GitHub;

use App\Models\Translations\Project;
use App\Models\Translations\ProjectGitHubConfig;
use App\Repositories\GitHub\GitHubBranchRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group  external
 * @covers \App\Repositories\GitHub\GitHubRepository
 */
class GitHubBranchRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Models\Translations\Project
     */
    private $project;

    /**
     * @var \App\Repositories\GitHub\GitHubRepository
     */
    private $gitHubRepository;

    /**
     * @var \App\Models\Translations\ProjectGitHubConfig
     */
    private $gitConfig;

    public function setUp(): void
    {
        parent::setUp();

        $user = $this->user();

        /* @var \App\Models\Translations\Project $project */
        $this->project = factory(Project::class)->create();
        $this->project->setOwner($user);

        $this->gitConfig = factory(ProjectGitHubConfig::class)->create(
            [
                'access_token' => env('GIT_HUB_TEST_TOKEN'),
                'username'     => env('GIT_HUB_TEST_USER'),
                'repository'   => env('GIT_HUB_TEST_REPO'),
                'project_id'   => $this->project->id,
            ]
        );

        $this->gitHubRepository = resolve(GitHubBranchRepository::class);
    }

    /** @test */
    public function a_new_branch_can_be_created()
    {
        // Given
        $branchName = 'featureTest';
        $ref        = "refs/heads/{$branchName}";

        // When
        $response = $this->gitHubRepository->createBranch($this->project, $branchName);

        $delete = $this->gitHubRepository->removeBranch($this->project, $branchName);

        // Then
        $this->assertIsArray($response);
        $this->assertSame($ref, $response['ref']);
        $this->assertEmpty($delete);
    }
}
