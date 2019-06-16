<?php

namespace Tests\Feature\Repository;

use App\Models\Translations\Project;
use App\Models\Translations\ProjectGitHubConfig;
use App\Models\User;
use App\Repositories\GitHub\GitHubRepoRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @group  feature
 * @covers \App\Http\Controllers\Git\RepositoryController::index
 */
class ListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->get(route('repositories.index', [123]));

        // Then
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_has_no_project_access()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($owner);

        // When
        $response = $this->signIn($user)->get(route('repositories.index', [$project->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_requesting_a_project_repositories()
    {
        // Given
        $this->mock(GitHubRepoRepository::class, function ($mock) {
            /* @var \Mockery\Mock $mock */
            $mock->shouldReceive('getRepositoryList')
                ->withAnyArgs()
                ->andReturn($this->loadFixture('git/repositories.list'))
            ;
        });

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($user);

        factory(ProjectGitHubConfig::class)->create(
            [
                'access_token' => env('GIT_HUB_TEST_TOKEN'),
                'username'     => env('GIT_HUB_TEST_USER'),
                'repository'   => env('GIT_HUB_TEST_REPO'),
                'project_id'   => $project->id,
            ]
        );

        // When
        $response = $this->signIn($user)->get(route('repositories.index', [$project->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $this->assertSame($project->gitHubConfig->repository, $response->json()['data'][0]['name']);
    }
}
