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
class StoreTest extends TestCase
{
    use RefreshDatabase;
    use
        CreateActiveSubscription;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->post(route('teams.store'));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_creates_a_new_team_without_team_quota()
    {
        // Given
        Event::fake([CollaboratorAddedToTeam::class]);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $collaborator */
        $collaborator = factory(User::class)->create();

        $this->createActiveSubscription(
            $user,
            'professional-month-eur',
            ['team_count' => 0]);

        $data = [
            'name'          => $this->faker->word,
            'description'   => $this->faker->text,
            'collaborators' => [
                [
                    'id'    => $collaborator->uuid,
                    'role'  => Team::TEAM_DEFAULT_ROLE_ALIAS,
                    'owner' => false,
                ],
            ],
        ];

        // When
        $response = $this->signIn($user)->post(route('teams.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        Event::assertNotDispatched(CollaboratorAddedToTeam::class);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_creates_a_new_team_without_collaborator_quota()
    {
        // Given
        Event::fake([CollaboratorAddedToTeam::class]);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $collaborator */
        $collaborator = factory(User::class)->create();

        $this->createActiveSubscription(
            $user,
            'professional-month-eur',
            ['collaborator_count' => 0]);

        $data = [
            'name'          => $this->faker->word,
            'description'   => $this->faker->text,
            'teams'         => [],
            'collaborators' => [
                [
                    'id'    => $collaborator->uuid,
                    'role'  => Team::TEAM_DEFAULT_ROLE_ALIAS,
                    'owner' => false,
                ],
            ],
        ];

        // When
        $response = $this->signIn($user)->post(route('teams.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        Event::assertNotDispatched(CollaboratorAddedToTeam::class);
    }

    /** @test */
    public function a_200_will_be_returned_if_the_user_creates_a_new_team()
    {
        // Given
        Event::fake([CollaboratorAddedToTeam::class]);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $collaborator1 */
        $collaborator1 = factory(User::class)->create();

        /** @var \App\Models\User $collaborator2 */
        $collaborator2 = factory(User::class)->create();

        /** @var \App\Models\User $collaborator3 */
        $collaborator3 = factory(User::class)->create();

        $this->createActiveSubscription($user, 'professional-month-eur');

        $data = [
            'name'          => $this->faker->word,
            'description'   => $this->faker->text,
            'teams'         => [],
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
                [
                    'id'    => $collaborator3->uuid,
                    'role'  => Team::TEAM_DEFAULT_ROLE_ALIAS,
                    'owner' => false,
                ],
            ],
        ];

        // When
        $response = $this->signIn($user)->post(route('teams.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['name' => $data['name']]);
        $this->assertDatabaseHas('teams', [
            'uuid' => $response->json('data')['id'],
        ]);
        $this->assertDatabaseHas('collaborators', [
            'entity_type' => 'team',
            'is_owner'    => 1,
            'user_id'     => $user->id,
        ]);
        $this->assertDatabaseHas('collaborators', [
            'entity_type' => 'team',
            'is_owner'    => 0,
            'is_valid'    => 0,
            'user_id'     => $collaborator1->id,
        ]);
        $this->assertDatabaseHas('collaborators', [
            'entity_type' => 'team',
            'is_owner'    => 0,
            'is_valid'    => 0,
            'user_id'     => $collaborator2->id,
        ]);
        $this->assertDatabaseHas('collaborators', [
            'entity_type' => 'team',
            'is_owner'    => 0,
            'is_valid'    => 0,
            'user_id'     => $collaborator3->id,
        ]);

        Event::assertDispatched(CollaboratorAddedToTeam::class);
    }
}
