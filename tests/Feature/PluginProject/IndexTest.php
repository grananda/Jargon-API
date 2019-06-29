<?php

namespace Tests\Feature\PluginProject;

use App\Models\Translations\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @coversNothing
 */
class IndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->get(route('plugin.index', [123]));

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
        $response = $this->signIn($user)->get(route('plugin.index', [$project->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_if_the_user_has_project_access()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($user);

        // When
        $response = $this->signIn($user)->get(route('plugin.index', [$project->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_OK);
    }
}
