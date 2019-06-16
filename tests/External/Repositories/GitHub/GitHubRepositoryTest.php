<?php

namespace Tests\External\Repositories\GitHub;

use App\Exceptions\GitHubConnectionException;
use App\Models\Translations\Project;
use App\Models\Translations\ProjectGitHubConfig;
use App\Repositories\GitHub\GitHubRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group  external
 * @covers \App\Repositories\GitHub\GitHubRepository
 */
class GitHubRepositoryTest extends TestCase
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

        $this->gitHubRepository = resolve(GitHubRepository::class);
    }

    /** @test */
    public function an_exception_is_thrown_when_no_git_hub_has_been_related_to_project()
    {
        // Given
        $this->expectException(GitHubConnectionException::class);

        $this->project->gitHubConfig = null;

        // When
        $this->gitHubRepository->getRepositoryList($this->project);
    }

    /** @test */
    public function get_repository_list()
    {
        // When
        $response = $this->gitHubRepository->getRepositoryList($this->project);

        // Then
        $this->assertIsArray($response);
        $this->assertSame($this->gitConfig->repository, $response[0]['name']);
    }

    /** @test */
    public function repository_information_can_be_retrieved()
    {
        // When
        $response = $this->gitHubRepository->getRepositoryDetails($this->project, $this->project->gitHubConfig->repository);

        // Then
        $this->assertIsArray($response);
        $this->assertSame($this->gitConfig->repository, $response['name']);
    }

    /**
     * @throws \App\Exceptions\GitHubConnectionException
     * @throws \Github\Exception\MissingArgumentException
     */
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
