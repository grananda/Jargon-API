<?php

namespace Tests\Feature\Branch;

use App\Models\Translations\GitConfig;
use App\Models\Translations\Project;
use App\Models\User;
use App\Services\GitHub\GitHubBranchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @group  feature
 * @covers \App\Http\Controllers\Git\BranchController::delete
 */
class DeleteTest extends TestCase
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
        $response = $this->delete(route('branches.destroy', [123]), $data);

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
        $response = $this->signIn($user)->delete(route('branches.destroy', [$project->uuid]), $data);

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_requesting_a_project_repositories()
    {
        // Given
        $this->mock(GitHubBranchService::class, function ($mock) {
            /* @var \Mockery\Mock $mock */
            $mock->shouldReceive('removeBranch')
                ->withAnyArgs()
                ->andReturn($this->loadFixture('git/references.remove'))
            ;
        });

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($user);

        factory(GitConfig::class)->create(
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
        $response = $this->signIn($user)->delete(route('branches.destroy', [$project->uuid]), $data);

        // Then
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
