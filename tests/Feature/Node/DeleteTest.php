<?php

namespace Tests\Feature\Node;

use App\Models\Organization;
use App\Models\Translations\Node;
use App\Models\Translations\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group feature
 * @covers \App\Http\Controllers\Node\NodeController::destroy
 */
class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->delete(route('nodes.destroy', [123]));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_has_no_node_access()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $owner */
        $owner = $this->user();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($owner);

        /** @var \App\Models\Translations\Node $node1_1 */
        $node = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        // When
        $response = $this->signIn($user)->delete(route('nodes.destroy', [$node->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_has_no_role_node_access()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $owner */
        $owner = $this->user();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($owner);
        $project->setMember($user, Project::PROJECT_TRANSLATOR_ROLE_ALIAS);

        /** @var \App\Models\Translations\Node $node1_1 */
        $node = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        // When
        $response = $this->signIn($user)->delete(route('nodes.destroy', [$node->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_deleting_a_non_root_node()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($user);

        /** @var \App\Models\Translations\Node $node */
        $node = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        /** @var \App\Models\Translations\Node $nodeChildren1 */
        $nodeChildren1 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ], $node);

        /** @var \App\Models\Translations\Node $nodeChildren2 */
        $nodeChildren2 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 1,
            'project_id' => $project->id,
        ], $node);

        /** @var \App\Models\Translations\Node $nodeChildren3 */
        $nodeChildren3 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 2,
            'project_id' => $project->id,
        ], $node);

        // When
        $response = $this->signIn($user)->delete(route('nodes.destroy', [$nodeChildren2->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('nodes', [
            'uuid' => $nodeChildren2->uuid,
        ]);
        $this->assertDatabaseHas('nodes', [
            'uuid'       => $nodeChildren1->uuid,
            'sort_index' => 0,
        ]);
        $this->assertDatabaseHas('nodes', [
            'uuid'       => $nodeChildren3->uuid,
            'sort_index' => 1,
        ]);
    }

    /** @test */
    public function a_200_will_be_returned_when_deleting_a_root_node()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($user);

        /** @var \App\Models\Translations\Node $node1 */
        $node1 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        /** @var \App\Models\Translations\Node $node2 */
        $node2 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 1,
            'project_id' => $project->id,
        ]);

        /** @var \App\Models\Translations\Node $node3 */
        $node3 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 2,
            'project_id' => $project->id,
        ], $node2);

        // When
        $response = $this->signIn($user)->delete(route('nodes.destroy', [$node2->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('nodes', [
            'uuid' => $node2->uuid,
        ]);
        $this->assertDatabaseHas('nodes', [
            'uuid'       => $node1->uuid,
            'sort_index' => 0,
        ]);
        $this->assertDatabaseMissing('nodes', [
            'uuid'       => $node3->uuid,
            'sort_index' => 1,
        ]);
    }
}
