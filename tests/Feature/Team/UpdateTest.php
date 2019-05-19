<?php

namespace Tests\Feature\Api\Team;

use App\Events\Collaborator\CollaboratorAddedToTeam;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

/**
 * @group feature
 * @coversNothing
 */
class UpdateTest extends TestCase
{
    use RefreshDatabase;
    use
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
    public function a_403_will_be_returned_if_a_non_owner_updates_a_team()
    {
        // Given
        Event::fake(CollaboratorAddedToTeam::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();
        $this->signIn($owner);

        $this->createActiveSubscription($owner, 'professional-month-eur');

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);
        $team->setMember($user, Team::TEAM_DEFAULT_ROLE_ALIAS);

        $data = [
            'name' => $this->faker->sentence,
        ];

        // When
        $response = $this->signIn($user)->put(route('teams.update', [$team->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        Event::assertDispatched(CollaboratorAddedToTeam::class, 1);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_updates_a_team_without_collaborator_quota()
    {
        // Given
        Event::fake(CollaboratorAddedToTeam::class);

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
            'professional-month-eur',
            ['collaborator_count' => 1]);

        $data = [
            'name'          => $this->faker->sentence,
            'collaborators' => [
                [
                    'id'    => $collaborator1->uuid,
                    'role'  => Team::TEAM_DEFAULT_ROLE_ALIAS,
                    'owner' => false,
                ],
                [
                    'id'    => $collaborator2->uuid,
                    'role'  => Team::TEAM_DEFAULT_ROLE_ALIAS,
                    'owner' => false,
                ],
            ],
        ];

        // When
        $response = $this->signIn($owner)->put(route('teams.update', [$team->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        Event::assertDispatched(CollaboratorAddedToTeam::class, 1);
    }

    /** @test */
    public function a_200_will_be_returned_if_an_owner_updates_an_team()
    {
        /* @var \App\Models\User $owner */
        Event::fake(CollaboratorAddedToTeam::class);

        /** @var User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\User $collaborator1 */
        $collaborator1 = factory(User::class)->create();

        /** @var \App\Models\User $collaborator2 */
        $collaborator2 = factory(User::class)->create();

        /** @var \Illuminate\Database\Eloquent\Collection $collaborator1 */
        $collaborators = factory(User::class, 5)->create();

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);
        $team->setMember($collaborator1, Team::TEAM_DEFAULT_ROLE_ALIAS);
        $team->setMember($collaborator2, Team::TEAM_DEFAULT_ROLE_ALIAS);
        $team->validateMember($collaborator2);

        $collaborators->each(function ($collaborator) use ($team) {
            $team->setMember($collaborator, Team::TEAM_TRANSLATOR_ROLE_ALIAS);
        });

        $this->createActiveSubscription($owner, 'professional-month-eur');
        $data = [
            'name'          => $this->faker->sentence,
            'collaborators' => [
                [
                    'id'    => $collaborator1->uuid,
                    'role'  => Team::TEAM_DEFAULT_ROLE_ALIAS,
                    'owner' => false,
                ],
                [
                    'id'    => $collaborator2->uuid,
                    'role'  => Team::TEAM_DEFAULT_ROLE_ALIAS,
                    'owner' => false,
                ],
            ],
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
        $this->assertDatabaseHas('collaborators', [
            'entity_type' => 'team',
            'is_owner'    => 0,
            'is_valid'    => false,
            'user_id'     => $collaborator1->id,
        ]);

        $this->assertDatabaseHas('collaborators', [
            'entity_type' => 'team',
            'is_owner'    => 0,
            'is_valid'    => true,
            'user_id'     => $collaborator2->id,
        ]);

        $collaborators->each(function ($item) {
            $this->assertDatabaseMissing('collaborators', [
                'entity_type' => 'team',
                'is_owner'    => 0,
                'is_valid'    => false,
                'user_id'     => $item->id,
            ]);
        });

        Event::assertDispatched(CollaboratorAddedToTeam::class, 7);
    }
}
