<?php

namespace Tests\Feature\Api\Team;


use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\Response;
use Tests\traits\CreateActiveSubscription;

class UpdateTest extends TestCase
{
    use RefreshDatabase,
        CreateActiveSubscription;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->put(route('teams.update', [1]));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_a_non_owner_updates_an_team()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();
        $this->signIn($owner);

        $this->createActiveSubscription($owner, 'professional');

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);
        $team->setMember($user, Team::TEAM_DEFAULT_ROLE_ALIAS);

        $data = [
            'name'          => $this->faker->sentence,
            'collaborators' => [],
        ];

        // When
        $response = $this->signIn($user)->put(route('teams.update', [$team->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_updates_a_team_without_collaborator_quota()
    {
        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\User $collaborator1 */
        $collaborator1 = factory(User::class)->create();

        /** @var \App\Models\User $collaborator2 */
        $collaborator2 = factory(User::class)->create();

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);
        $team->setMember($collaborator1, Team::TEAM_TRANSLATOR_ROLE_ALIAS);


        $this->createActiveSubscription(
            $owner,
            'professional',
            ['collaborator_count' => 1]);

        $data = [
            'name'          => $this->faker->sentence,
            'collaborators' => [
                [$collaborator1->uuid, Team::TEAM_DEFAULT_ROLE_ALIAS],
                [$collaborator2->uuid, Team::TEAM_DEFAULT_ROLE_ALIAS],
            ],
        ];

        // When
        $response = $this->signIn($owner)->put(route('teams.update', [$team->uuid]), $data);


        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_if_an_owner_updates_an_team()
    {
        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);
        $team->setMember($user, Team::TEAM_TRANSLATOR_ROLE_ALIAS);

        $this->createActiveSubscription(
            $owner,
            'professional',
            ['collaborator_count' => 1]);

        $data = [
            'name'          => $this->faker->sentence,
            'collaborators' => [],
        ];

        // When
        $response = $this->signIn($owner)->put(route('teams.update', [$team->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['name' => $data['name']]);
        $this->assertDatabaseHas('teams', [
            'uuid' => $response->json('data')['id'],
            'name' => $response->json('data')['name'],
        ]);
        $this->assertDatabaseHas('collaborators', [
            'entity_type' => 'team',
            'is_owner'    => 1,
            'user_id'     => $owner->id,
        ]);
    }
}
