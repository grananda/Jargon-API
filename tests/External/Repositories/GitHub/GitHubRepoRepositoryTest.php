<?php

namespace Tests\External\Repositories\GitHub;

use App\Exceptions\GitHubConnectionException;
use App\Models\Translations\Project;
use App\Models\Translations\ProjectGitHubConfig;
use App\Repositories\GitHub\GitHubRepoRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group  external
 * @covers \App\Repositories\GitHub\GitHubRepository
 */
class GitHubRepoRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Models\Translations\Project
     */
    private $project;

    /**
     * @var \App\Repositories\GitHub\GitHubRepository
     */
    private $gitHubRepoRepository;

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

        $this->gitHubRepoRepository = resolve(GitHubRepoRepository::class);
    }

    /** @test */
    public function an_exception_is_thrown_when_no_git_hub_has_been_related_to_project()
    {
        // Given
        $this->expectException(GitHubConnectionException::class);

        $this->project->gitHubConfig = null;

        // When
        $this->gitHubRepoRepository->getRepositoryList($this->project);
    }

    /** @test */
    public function get_repository_list()
    {
        // When
        $response = $this->gitHubRepoRepository->getRepositoryList($this->project);

        // Then
        $this->assertIsArray($response);
        $this->assertSame($this->gitConfig->repository, $response[0]['name']);
    }

    /** @test */
    public function repository_information_can_be_retrieved()
    {
        // When
        $response = $this->gitHubRepoRepository->getRepositoryDetails($this->project, $this->project->gitHubConfig->repository);

        // Then
        $this->assertIsArray($response);
        $this->assertSame($this->gitConfig->repository, $response['name']);
    }
}
