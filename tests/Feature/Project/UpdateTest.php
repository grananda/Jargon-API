<?php

namespace Tests\Feature\Project;

use App\Events\Collaborator\CollaboratorAddedToProject;
use App\Models\Dialect;
use App\Models\Organization;
use App\Models\Team;
use App\Models\Translations\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

/**
 * @group feature
 * @covers \App\Http\Controllers\Project\ProjectController::update
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
        $response = $this->put(route('projects.update', [1]));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_a_non_owner_updates_an_project()
    {
        // Given
        Event::fake(CollaboratorAddedToProject::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();
        $this->signIn($owner);

        $this->createActiveSubscription($owner, 'professional-month-eur');

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($owner);
        $project->setMember($user);

        $data = [
            'title' => $this->faker->sentence,
        ];

        // When
        $response = $this->signIn($user)->put(route('projects.update', [$project->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        Event::assertDispatched(CollaboratorAddedToProject::class, 1);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_updates_a_project_without_collaborator_quota()
    {
        // Given
        Event::fake(CollaboratorAddedToProject::class);

        /** @var \App\Models\User $collaborator1 */
        $collaborator1 = factory(User::class)->create();

        /** @var \App\Models\User $collaborator2 */
        $collaborator2 = factory(User::class)->create();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();
        $this->signIn($owner);

        $this->createActiveSubscription($owner, 'professional-month-eur', ['collaborator_count' => 1]);

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($owner);
        $project->setMember($collaborator1);

        $data = [
            'title'         => $this->faker->sentence,
            'collaborators' => [
                [
                    'id'    => $collaborator1->uuid,
                    'role'  => Project::PROJECT_DEFAULT_ROLE_ALIAS,
                    'owner' => false,
                ],
                [
                    'id'    => $collaborator2->uuid,
                    'role'  => Project::PROJECT_DEFAULT_ROLE_ALIAS,
                    'owner' => false,
                ],
            ],
        ];

        // When
        $response = $this->signIn($owner)->put(route('projects.update', [$project->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        Event::assertDispatched(CollaboratorAddedToProject::class, 1);
    }

    /** @test */
    public function a_200_will_be_returned_if_an_owner_updates_an_project()
    {
        // Given
        Event::fake(CollaboratorAddedToProject::class);

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\User $collaborator1 */
        $collaborator1 = factory(User::class)->create();

        /** @var \App\Models\User $collaborator2 */
        $collaborator2 = factory(User::class)->create();

        /** @var \Illuminate\Database\Eloquent\Collection $collaborator1 */
        $collaborators = factory(User::class, 5)->create();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($owner);

        /** @var \App\Models\Dialect $dialect1 */
        $dialect1 = Dialect::where('locale', 'es_MX')->first();

        /** @var \App\Models\Dialect $dialect */
        $dialect2 = Dialect::where('locale', 'es_ES')->first();

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($owner);
        $project->setMember($collaborator1);
        $project->setMember($collaborator2);
        $project->validateMember($collaborator2);
        $project->setDialects([$dialect1->id]);

        $collaborators->each(function ($collaborator) use ($project) {
            $project->setMember($collaborator);
        });

        $this->createActiveSubscription($owner, 'professional-month-eur');

        $data = [
            'title'         => $this->faker->sentence,
            'collaborators' => [
                [
                    'id'    => $collaborator1->uuid,
                    'role'  => Project::PROJECT_DEFAULT_ROLE_ALIAS,
                    'owner' => false,
                ],
                [
                    'id'    => $collaborator2->uuid,
                    'role'  => Project::PROJECT_DEFAULT_ROLE_ALIAS,
                    'owner' => false,
                ],
            ],
            'teams' => [
                [
                    $team->uuid,
                ],
            ],
            'dialects' => [
                [
                    'locale'  => $dialect1->uuid,
                    'default' => true,
                ],
                [
                    'locale'  => $dialect2->uuid,
                    'default' => false,
                ],
            ],
        ];

        // When
        $response = $this->signIn($owner)->put(route('projects.update', [$project->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['title' => $data['title']]);
        $this->assertDatabaseHas('project_team', [
            'project_id' => $project->id,
            'team_id'    => $team->id,
        ]);
        $this->assertDatabaseHas('collaborators', [
            'entity_type' => 'project',
            'is_owner'    => 0,
            'is_valid'    => false,
            'user_id'     => $collaborator1->id,
        ]);
        $this->assertDatabaseHas('collaborators', [
            'entity_type' => 'project',
            'is_owner'    => 0,
            'is_valid'    => true,
            'user_id'     => $collaborator2->id,
        ]);

        $collaborators->each(function ($item) {
            $this->assertDatabaseMissing('collaborators', [
                'entity_type' => 'project',
                'is_owner'    => 0,
                'is_valid'    => false,
                'user_id'     => $item->id,
            ]);
        });

        $this->assertDatabaseHas('dialect_project', [
            'project_id' => $project->id,
            'is_default' => true,
            'dialect_id' => $dialect1->id,
        ]);
        $this->assertDatabaseHas('dialect_project', [
            'project_id' => $project->id,
            'is_default' => false,
            'dialect_id' => $dialect2->id,
        ]);

        Event::assertDispatched(CollaboratorAddedToProject::class, 7);
    }
}
