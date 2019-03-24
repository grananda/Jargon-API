<?php

namespace Tests\Feature\Project;


use App\Models\Organization;
use App\Models\Team;
use App\Models\Translations\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

class UpdateTest extends TestCase
{
    use RefreshDatabase,
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
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();
        $this->signIn($owner);

        $this->createActiveSubscription($owner, 'professional');

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($owner);
        $project->setMember($user);

        $data = [
            'title'         => $this->faker->sentence,
            'collaborators' => [],
        ];

        // When
        $response = $this->signIn($user)->put(route('projects.update', [$project->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_updates_a_project_without_collaborator_quota()
    {
        // Given
        /** @var \App\Models\User $collaborator1 */
        $collaborator1 = factory(User::class)->create();

        /** @var \App\Models\User $collaborator2 */
        $collaborator2 = factory(User::class)->create();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();
        $this->signIn($owner);

        $this->createActiveSubscription($owner, 'professional', ['collaborator_count' => 1]);

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($owner);
        $project->setMember($collaborator1);

        $data = [
            'title'         => $this->faker->sentence,
            'collaborators' => [
                [$collaborator1->uuid, Project::PROJECT_DEFAULT_ROLE_ALIAS],
                [$collaborator2->uuid, Project::PROJECT_DEFAULT_ROLE_ALIAS],
            ],
        ];

        // When
        $response = $this->signIn($owner)->put(route('projects.update', [$project->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_if_an_owner_updates_an_project()
    {
        // Given
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

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($owner);
        $project->setMember($collaborator1);

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
            'user_id'     => $collaborator1->id,
        ]);
    }
}