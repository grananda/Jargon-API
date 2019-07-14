<?php

namespace Tests\Feature\Organization;

use App\Models\Organization;
use App\Models\Team;
use App\Models\Translations\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group feature
 * @covers \App\Http\Controllers\Organization\OrganizationApiController::show
 */
class ShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($user);

        // When
        $response = $this->get(route('organizations.show', [$organization->uuid]));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_has_no_organization_access_as_owner()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\Organization $organization1 */
        $organization1 = factory(Organization::class)->create();
        $organization1->setOwner($owner);

        /** @var \App\Models\Organization $organization2 */
        $organization2 = factory(Organization::class)->create();
        $organization2->setOwner($user);

        // When
        $response = $this->signIn($user)->get(route('organizations.show', [$organization1->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_when_showing_an_organization_to_a_non_valid_team_member()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\Organization $organization1 */
        $organization1 = factory(Organization::class)->create();
        $organization1->setOwner($owner);

        /** @var \App\Models\Organization $organization2 */
        $organization2 = factory(Organization::class)->create();
        $organization2->setOwner($user);

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);
        $team->setMember($user);

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($owner);
        $project->setTeams([$team->id]);

        // When
        $response = $this->signIn($user)->get(route('organizations.show', [$organization1->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_when_showing_an_organization_to_a_non_valid_project_member()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\Organization $organization1 */
        $organization1 = factory(Organization::class)->create();
        $organization1->setOwner($owner);

        /** @var \App\Models\Organization $organization2 */
        $organization2 = factory(Organization::class)->create();
        $organization2->setOwner($user);

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($owner);
        $project->setMember($user);

        // When
        $response = $this->signIn($user)->get(route('organizations.show', [$organization1->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_showing_an_organization_to_owner()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($user);

        // When
        $response = $this->signIn($user)->get(route('organizations.show', [$organization->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $organization->uuid,
        ]);
    }

    /** @test */
    public function a_200_will_be_returned_when_showing_an_organization_to_a_valid_team_member()
    {
        // Given
        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Organization $organization1 */
        $organization1 = factory(Organization::class)->create();
        $organization1->setOwner($owner);

        /** @var \App\Models\Organization $organization2 */
        $organization2 = factory(Organization::class)->create();
        $organization2->setOwner($user);

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);
        $team->setMember($user);
        $team->validateMember($user);

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization1);
        $project->setOwner($owner);
        $project->setTeams([
            $team->id,
        ]);

        // When
        $response = $this->signIn($user)->get(route('organizations.show', [$organization1->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $organization1->uuid]);
    }

    /** @test */
    public function a_200_will_be_returned_when_showing_an_organization_to_a_valid_project_member()
    {
        // Given
        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Organization $organization1 */
        $organization1 = factory(Organization::class)->create();
        $organization1->setOwner($owner);

        /** @var \App\Models\Organization $organization2 */
        $organization2 = factory(Organization::class)->create();
        $organization2->setOwner($user);

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization1);
        $project->setOwner($owner);
        $project->setMember($user);
        $project->validateMember($user);

        // When
        $response = $this->signIn($user)->get(route('organizations.show', [$organization1->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $organization1->uuid]);
    }
}
