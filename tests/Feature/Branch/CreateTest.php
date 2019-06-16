<?php

namespace Tests\Feature\Branch;

use App\Models\Translations\Project;
use App\Models\Translations\ProjectGitHubConfig;
use App\Models\User;
use App\Repositories\GitHub\GitHubBranchRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @group  feature
 * @covers \App\Http\Controllers\Git\BranchController::store
 */
class CreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // Given
        $data = [
            'branch' => 'test-branch',
        ];

        // When
        $response = $this->post(route('branches.store', [123]), $data);

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

        $data = [
            'branch' => 'test-branch',
        ];

        // When
        $response = $this->signIn($user)->post(route('branches.store', [$project->uuid]), $data);

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_requesting_a_project_repositories()
    {
        // Given
        $this->mock(GitHubBranchRepository::class, function ($mock) {
            /* @var \Mockery\Mock $mock */
            $mock->shouldReceive('createBranch')
                ->withAnyArgs()
                ->andReturn($this->loadFixture('git/references.create'))
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

        $data = [
            'branch' => 'featureTest',
        ];

        $ref = "refs/heads/{$data['branch']}";

        // When
        $response = $this->signIn($user)->post(route('branches.store', [$project->uuid]), $data);

        // Then
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertSame($ref, $response->json()['data']['ref']);
    }
}
