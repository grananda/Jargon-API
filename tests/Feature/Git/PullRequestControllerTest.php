<?php

namespace Tests\Feature\Git;

use App\Jobs\CreateProjectPullRequest;
use App\Models\Translations\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * @coversNothing
 */
class PullRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_is_returned_when_not_logged_in()
    {
        // When
        $response = $this->get(route('pullRequest.create', [123]));

        // Assert
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
        $response = $this->signIn($user)->get(route('pullRequest.create', [$project->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_is_returned_when_a_pull_request_is_queued()
    {
        // Given
        Bus::fake(CreateProjectPullRequest::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($user);

        // When
        $response = $this->signIn($user)->get(route('pullRequest.create', [$project->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_NO_CONTENT);

        Bus::assertDispatched(CreateProjectPullRequest::class);
    }
}
