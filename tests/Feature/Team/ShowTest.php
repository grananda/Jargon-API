<?php

namespace Tests\Feature\Team;


use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($user);

        // When
        $response = $this->get(route('teams.show', [$team->uuid]));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_has_no_organization_access()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);

        // When
        $response = $this->signIn($user)->get(route('teams.show', [$team->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_when_showing_an_organization_to_a_non_valid_member()
    {
        // Given
        $user = factory(User::class)->create();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);
        $team->addMember($user, Team::ORGANIZATION_DEFAULT_ROLE_ALIAS);

        // When
        $response = $this->signIn($user)->get(route('teams.show', [$team->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_showing_an_organization_to_owner()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($user);

        // When
        $response = $this->signIn($user)->get(route('teams.show', [$team->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $team->uuid,
        ]);
    }

    /** @test */
    public function a_200_will_be_returned_when_showing_an_organization_to_member()
    {
        // Given
        $user = factory(User::class)->create();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);
        $team->addMember($user, Team::ORGANIZATION_DEFAULT_ROLE_ALIAS);
        $team->validateMember($user);

        // When
        $response = $this->signIn($user)->get(route('teams.show', [$team->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $team->uuid,
        ]);
    }
}
