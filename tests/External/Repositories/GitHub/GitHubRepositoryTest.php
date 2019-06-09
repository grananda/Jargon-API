<?php

namespace Tests\External\Repositories\GitHub;

use App\Exceptions\GitHubConnectionException;
use App\Models\Translations\Project;
use App\Models\Translations\ProjectGitHubConfig;
use App\Repositories\GitHub\GitHubRepository;
use GrahamCampbell\GitHub\GitHubManager;
use Tests\TestCase;

/**
 * @group  external
 * @covers \App\Repositories\GitHub\GitHubRepository
 */
class GitHubRepositoryTest extends TestCase
{
    private $username;

    private $repo;

    public function setUp(): void
    {
        parent::setUp();

        $this->username = env('GIT_HUB_TEST_USER', null);

        $this->repo = env('GIT_HUB_TEST_REPO', null);
    }

    /** @test */
    public function an_exception_is_thrown_when_no_git_hub_has_been_related_to_project()
    {
        // Given
        $this->expectException(GitHubConnectionException::class);

        $user = $this->user();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($user);

        /** @var GitHubRepository $gitHubRepository */
        $gitHubRepository = resolve(GitHubRepository::class);

        // When
        $gitHubRepository->getRepositoryList($project);
    }

    /** @test */
    public function get_repository_list()
    {
        // Given
        $user = $this->user();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($user);

        /** @var \App\Models\Translations\ProjectGitHubConfig $ghConfig */
        $ghConfig = factory(ProjectGitHubConfig::class)->create(
            [
                'access_token' => env('GIT_HUB_TEST_TOKEN'),
                'username'     => env('GIT_HUB_TEST_USER'),
                'repository'   => env('GIT_HUB_TEST_REPO'),
                'project_id'   => $project->id,
            ]
        );

        /** @var GitHubRepository $gitHubRepository */
        $gitHubRepository = resolve(GitHubRepository::class);

        // When
        $response = $gitHubRepository->getRepositoryList($project);

        // Then
        $this->assertIsArray($response);
        $this->assertSame(env('GIT_HUB_TEST_REPO'), $response[0]['name']);
    }

    /** @test */
    public function repository_information_can_be_retrieved()
    {
        // Given
        /** @var GitHubManager $gitHub */
        $gitHub = resolve(GitHubManager::class);

        // When
        $response = $gitHub->repo()->show($this->username, $this->repo);

        // Then
        $this->assertIsArray($response);
        $this->assertSame($this->repo, $response['name']);
    }

    /** @test
     * @throws \Github\Exception\MissingArgumentException
     */
    public function a_new_branch_can_be_created()
    {
        // Given
        /** @var GitHubManager $gitHub */
        $gitHub = resolve(GitHubManager::class);

        $branchName = 'heads/featureTest';
        $ref        = 'refs/'.$branchName;
        $masterRef  = 'heads/master';

        // When
        $reference = $gitHub->gitData()
            ->references()
            ->show($this->username, $this->repo, $masterRef)
        ;

        $response = $gitHub->gitData()
            ->references()
            ->create($this->username, $this->repo,
                [
                    'ref' => $ref,
                    'sha' => $reference['object']['sha'],
                ]
            )
        ;

        $delete = $gitHub->gitData()
            ->references()
            ->remove($this->username, $this->repo, $branchName)
        ;

        // Then
        $this->assertIsArray($response);
        $this->assertSame($ref, $response['ref']);
        $this->assertEmpty($delete);
    }
}
