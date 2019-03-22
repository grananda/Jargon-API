<?php

namespace Tests\Feature\Project;


use App\Models\Translations\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // Given
        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();

        // When
        $response = $this->get(route('projects.show', [$project->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_has_no_project_access_as_owner()
    {
        /** TODO Complete show org-team test */
        $this->assertTrue(true);
    }

    /** @test */
    public function a_403_will_be_returned_when_showing_a_project_to_a_non_valid_team_member()
    {
        /** TODO Complete show org-team test */
        $this->assertTrue(true);
    }

    /** @test */
    public function a_403_will_be_returned_when_showing_a_project_to_a_non_valid_project_member()
    {
        /** TODO Complete show org-project test */
        $this->assertTrue(true);
    }

    /** @test */
    public function a_200_will_be_returned_when_showing_a_project_to_owner()
    {
        /** TODO Complete show org-project test */
        $this->assertTrue(true);
    }

    /** @test */
    public function a_200_will_be_returned_when_showing_a_project_to_a_valid_team_member()
    {
        /** TODO Complete show org-project test */
        $this->assertTrue(true);
    }

    /** @test */
    public function a_200_will_be_returned_when_showing_a_project_to_a_valid_project_member()
    {
        /** TODO Complete show org-project test */
        $this->assertTrue(true);
    }
}