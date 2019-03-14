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
    public function a_200_will_be_returned_when_listing_all_teams_for_a_non_valid_team_user()
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
//        $organization->validateMember($user);

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOrganization($organization);
        $team->setOwner($owner);
        $team->addMember($user, Team::TEAM_DEFAULT_ROLE_ALIAS);

        /** @var \App\Models\Team $team2 */
        $team2 = factory(Team::class)->create();
        $team2->setOrganization($organization);
        $team2->setOwner($owner);

        // When
        $response = $this->signIn($user)->get(route('teams.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonMissingExact(['id' => $organization->uuid]);
    }

    /** @test */
    public function a_200_will_be_returned_when_listing_all_teams_for_an_owner()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($user);

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOrganization($organization);
        $team->setOwner($user);

        factory(Team::class)->create();

        // When
        $response = $this->signIn($user)->get(route('teams.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $team->uuid]);
        $response->assertJsonCount(1);
    }

    /** @test */
    public function a_200_will_be_returned_when_listing_all_teams_for_an_user()
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
        $organization->validateMember($user);

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOrganization($organization);
        $team->setOwner($owner);
        $team->addMember($user, Team::TEAM_DEFAULT_ROLE_ALIAS);
        $team->validateMember($user);

        /** @var \App\Models\Team $team2 */
        $team2 = factory(Team::class)->create();
        $team2->setOrganization($organization);
        $team2->setOwner($owner);

        // When
        $response = $this->signIn($user)->get(route('teams.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $team->uuid]);
        $response->assertJsonCount(1);
    }
}
