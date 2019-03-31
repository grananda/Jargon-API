<?php

namespace Tests\Feature\Project;


use App\Events\Collaborator\CollaboratorAddedToProject;
use App\Models\Organization;
use App\Models\Team;
use App\Models\Translations\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

class StoreTest extends TestCase
{
    use RefreshDatabase,
        CreateActiveSubscription;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->post(route('projects.store'));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_creates_a_new_project_without_project_quota()
    {
        // Given
        Event::fake([CollaboratorAddedToProject::class]);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $collaborator */
        $collaborator = factory(User::class)->create();

        $this->createActiveSubscription(
            $user,
            'professional',
            ['project_count' => 0]);

        $data = [
            'title'          => $this->faker->sentence,
            'description'   => $this->faker->text,
            'collaborators' => [
                [$collaborator->uuid, Project::PROJECT_DEFAULT_ROLE_ALIAS],
            ],
        ];

        // When
        $response = $this->signIn($user)->post(route('projects.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        Event::assertNotDispatched(CollaboratorAddedToProject::class);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_creates_a_new_project_without_collaborator_quota()
    {
        // Given
        Event::fake([CollaboratorAddedToProject::class]);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $collaborator */
        $collaborator = factory(User::class)->create();

        $this->createActiveSubscription(
            $user,
            'professional',
            ['collaborator_count' => 0]);

        $data = [
            'name'          => $this->faker->word,
            'description'   => $this->faker->text,
            'teams'         => [],
            'collaborators' => [
                [$collaborator->uuid, Project::PROJECT_DEFAULT_ROLE_ALIAS],
            ],
        ];

        // When
        $response = $this->signIn($user)->post(route('projects.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        Event::assertNotDispatched(CollaboratorAddedToProject::class);
    }

    /** @test */
    public function a_200_will_be_returned_if_the_user_creates_a_new_project()
    {
        // Given
        Event::fake([CollaboratorAddedToProject::class]);

        /** @var \App\Models\User $collaborator1 */
        $collaborator1 = factory(User::class)->create();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();
        $this->signIn($owner);

        $this->createActiveSubscription($owner, 'professional');

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($owner);

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);

        $data = [
            'title'         => $this->faker->sentence,
            'collaborators' => [
                [$collaborator1->uuid, Project::PROJECT_DEFAULT_ROLE_ALIAS,],
            ],
            'teams'         => [
                [$team->uuid,],
            ],
        ];

        // When
        $response = $this->signIn($owner)->post(route('projects.store'), $data);

        /** @var Project $project */
        $project = Project::findByUuidOrFail($response->json('data')['id']);

        // Assert
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['title' => $data['title']]);
        $this->assertDatabaseHas('project_team', [
            'project_id' => $project->id,
            'team_id'    => $team->id,
        ]);
        $this->assertDatabaseHas('collaborators', [
            'entity_type' => 'project',
            'is_owner'    => 0,
            'user_id'     => $collaborator1->id,
        ]);

        Event::assertDispatched(CollaboratorAddedToProject::class);
    }
}
