<?php

namespace Tests\Feature\Team;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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
    public function a_200_will_be_returned_when_listing_all_teams_for_a_non_member()
    {
        // Given
        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);

        // When
        $response = $this->signIn($user)->get(route('teams.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    /** @test */
    public function a_200_will_be_returned_when_listing_all_teams_for_non_valid_member()
    {
        // Given
        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);
        $team->setMember($user, Team::TEAM_DEFAULT_ROLE_ALIAS);

        // When
        $response = $this->signIn($user)->get(route('teams.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(0, 'data');

    }

    /** @test */
    public function a_200_will_be_returned_when_listing_all_teams_for_a_valid_member()
    {
        // Given
        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);
        $team->setMember($user, Team::TEAM_DEFAULT_ROLE_ALIAS);
        $team->validateMember($user);

        /** @var \App\Models\Team $team2 */
        $team2 = factory(Team::class)->create();
        $team2->setOwner($owner);
        $team2->setMember($user, Team::TEAM_DEFAULT_ROLE_ALIAS);

        /** @var \App\Models\Team $team3 */
        $team3 = factory(Team::class)->create();
        $team3->setOwner($owner);

        // When
        $response = $this->signIn($user)->get(route('teams.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['id' => $team->uuid]);
        $response->assertJsonMissingExact(['id' => $team2->uuid]);
    }

    /** @test */
    public function a_200_will_be_returned_when_listing_all_teams_for_an_owner()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $user2 */
        $user2 = factory(User::class)->create();

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($user);

        /** @var \App\Models\Team $team2 */
        $team2 = factory(Team::class)->create();
        $team2->setOwner($user2);

        factory(Team::class)->create();

        // When
        $response = $this->signIn($user)->get(route('teams.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $team->uuid]);
        $response->assertJsonMissing(['id' => $team2->uuid]);
    }
}
