<?php

namespace Tests\Feature\Team;

use App\Models\Organization;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->get(route('teams.index'));

        // Then
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_200_will_be_returned_when_listing_all_teams_for_a_user()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($user);

        /** @var \App\Models\Team $team2 */
        $team2 = factory(Team::class)->create();

        // When
        $response = $this->signIn($user)->get(route('teams.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $team->uuid]);
        $response->assertJsonMissing(['id' => $team2->uuid]);
    }

    /** @test */
    public function a_200_will_be_returned_when_listing_all_teams_for_an_owner_with_organization_scope()
    {
        // Given
        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($owner);

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);
        $team->setOrganization($organization);

        // When
        $response = $this->signIn($owner)->get(route('teams.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $team->uuid]);
    }

    /** @test */
    public function a_200_will_be_returned_when_listing_all_teams_for_a_member_with_organization_scope()
    {
        // Given
        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($owner);
        $organization->addMember($user, Organization::ORGANIZATION_DEFAULT_ROLE_ALIAS);


        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);
        $team->setOrganization($organization);
        $team->addMember($user, Team::TEAM_MANAGER_ROLE_ALIAS);

        /** @var \App\Models\Team $team2 */
        $team2 = factory(Team::class)->create();
        $team2->setOwner($owner);
        $team2->setOrganization($organization);

        // When
        $response = $this->signIn($user)->get(route('teams.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $team->uuid]);
        $response->assertJsonMissing(['id' => $team2->uuid]);
    }
}
