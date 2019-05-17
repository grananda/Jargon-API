<?php

namespace Tests\Feature\NodeRelocation;

use App\Models\Organization;
use App\Models\Translations\Node;
use App\Models\Translations\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * Class CopyTest.
 *
 * @package Tests\Feature\NodeCopy
 * @coversNothing
 */
class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->put(route('nodes.move.update', [1234]), []);

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_when_moving_a_node_a_non_member_project()
    {
        // Given
        /** @var User $user */
        $user = $this->user();

        /** @var User $owner */
        $owner = $this->user();

        /** @var Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($owner);

        /** @var Node $node1 */
        $node1 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        /** @var Node $node2 */
        $node2 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 1,
            'project_id' => $project->id,
        ]);

        $data = [
            'parent' => $node1->uuid,
        ];

        // When
        $response = $this->signIn($user)->put(route('nodes.move.update', [$node2->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_when_moving_a_node_by_a_non_authorized_member_project()
    {
        // Given
        /** @var User $user */
        $user = $this->user();

        /** @var User $owner */
        $owner = $this->user();

        /** @var Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($owner);
        $project->setMember($user, Project::PROJECT_TRANSLATOR_ROLE_ALIAS);

        /** @var Node $node1 */
        $node1 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        /** @var Node $node2 */
        $node2 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 1,
            'project_id' => $project->id,
        ]);

        $data = [
            'parent' => $node1->uuid,
        ];

        // When
        $response = $this->signIn($user)->put(route('nodes.move.update', [$node2->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_when_moving_a_node_to_a_different_project()
    {
        // Given
        /** @var User $user */
        $user = $this->user();

        /** @var Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var Project $project1 */
        $project1 = factory(Project::class)->create();
        $project1->setOrganization($organization);
        $project1->setOwner($user);

        /** @var Project $project2 */
        $project2 = factory(Project::class)->create();
        $project2->setOrganization($organization);
        $project2->setOwner($user);

        /** @var Node $node1 */
        $node1 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project1->id,
        ]);

        /** @var Node $node2 */
        $node2 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 1,
            'project_id' => $project2->id,
        ]);

        $data = [
            'parent' => $node1->uuid,
        ];

        // When
        $response = $this->signIn($user)->put(route('nodes.move.update', [$node2->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_when_moving_a_node_into_same_node()
    {
        // Given
        /** @var User $user */
        $user = $this->user();

        /** @var Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($user);

        /** @var Node $node1 */
        $node1 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        $data = [
            'parent' => $node1->uuid,
        ];

        // When
        $response = $this->signIn($user)->put(route('nodes.move.update', [$node1->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_when_moving_a_node_into_a_child_node()
    {
        // Given
        /** @var User $user */
        $user = $this->user();

        /** @var Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($user);

        /** @var Node $node1 */
        $node1 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        /** @var Node $node1 */
        $node2 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ], $node1);

        /** @var Node $node1 */
        $node3 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ], $node2);

        $data = [
            'parent' => $node3->uuid,
        ];

        // When
        $response = $this->signIn($user)->put(route('nodes.move.update', [$node2->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_422_will_be_returned_when_moving_a_node_into_a_deep_child_node()
    {
        // Given
        /** @var User $user */
        $user = $this->user();

        /** @var Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($user);

        /** @var Node $node1 */
        $node1 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        /** @var Node $node1 */
        $node2 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ], $node1);

        /** @var Node $node3 */
        $node3 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ], $node2);

        $data = [
            'parent' => $node3->uuid,
        ];

        // When
        $response = $this->signIn($user)->put(route('nodes.move.update', [$node1->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_422_will_be_returned_when_moving_a_node_into_its_parent_node()
    {
        // Given
        /** @var User $user */
        $user = $this->user();

        /** @var Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($user);

        /** @var Node $node1 */
        $node1 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        /** @var Node $node1 */
        $node2 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ], $node1);

        $data = [
            'parent' => $node1->uuid,
        ];

        // When
        $response = $this->signIn($user)->put(route('nodes.move.update', [$node2->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_moving_a_root_node()
    {
        // Given
        /** @var User $user */
        $user = $this->user();

        /** @var Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($user);

        /** @var Node $root1 */
        $root1 = Node::create([
            'key'        => 'root1',
            'route'      => 'root1',
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        /** @var Node $node1 */
        $node1 = Node::create([
            'key'        => 'node1',
            'route'      => 'root1.node1',
            'sort_index' => 0,
            'project_id' => $project->id,
        ], $root1);

        /** @var Node $root2 */
        $root2 = Node::create([
            'key'        => 'root2',
            'route'      => 'root2',
            'sort_index' => 1,
            'project_id' => $project->id,
        ]);

        /** @var Node $node1 */
        $node2 = Node::create([
            'key'        => 'node2',
            'route'      => 'root2.node2',
            'sort_index' => 0,
            'project_id' => $project->id,
        ], $root2);

        $data = [
            'parent' => $root2->uuid,
        ];

        // When
        $response = $this->signIn($user)->put(route('nodes.move.update', [$root1->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('nodes', [
            'id'         => $root1->id,
            'route'      => 'root2.root1',
            'parent_id'  => $root2->id,
            'sort_index' => 0,
        ]);
        $this->assertDatabaseHas('nodes', [
            'id'         => $node1->id,
            'route'      => 'root2.root1.node1',
            'parent_id'  => $root1->id,
            'sort_index' => 0,
        ]);

        $this->assertDatabaseHas('nodes', [
            'id'         => $root2->id,
            'route'      => 'root2',
            'parent_id'  => null,
            'sort_index' => 0,
        ]);
        $this->assertDatabaseHas('nodes', [
            'id'         => $node2->id,
            'route'      => 'root2.node2',
            'parent_id'  => $root2->id,
            'sort_index' => 1,
        ]);
    }

    /** @test */
    public function a_200_will_be_returned_when_moving_a_child_node()
    {
        // Given
        /** @var User $user */
        $user = $this->user();

        /** @var Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($user);

        /** @var Node $root1 */
        $root1 = Node::create([
            'key'        => 'root1',
            'route'      => 'root1',
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        /** @var Node $root2 */
        $root2 = Node::create([
            'key'        => 'root2',
            'route'      => 'root2',
            'sort_index' => 1,
            'project_id' => $project->id,
        ]);

        /** @var Node $node1 */
        $node1 = Node::create([
            'key'        => 'node1',
            'route'      => 'root1,node1',
            'sort_index' => 0,
            'project_id' => $project->id,
        ], $root1);
        /** @var Node $node1 */
        $node2 = Node::create([
            'key'        => 'node2',
            'route'      => 'root2.node2',
            'sort_index' => 0,
            'project_id' => $project->id,
        ], $root2);

        $data = [
            'parent' => $root2->uuid,
        ];

        // When
        $response = $this->signIn($user)->put(route('nodes.move.update', [$node1->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('nodes', [
            'id'         => $root1->id,
            'route'      => 'root1',
            'parent_id'  => null,
            'sort_index' => 0,
        ]);
        $this->assertDatabaseHas('nodes', [
            'id'         => $node1->id,
            'route'      => 'root2.node1',
            'parent_id'  => $root2->id,
            'sort_index' => 0,
        ]);

        $this->assertDatabaseHas('nodes', [
            'id'         => $root2->id,
            'route'      => 'root2',
            'parent_id'  => null,
            'sort_index' => 1,
        ]);
        $this->assertDatabaseHas('nodes', [
            'id'         => $node2->id,
            'route'      => 'root2.node2',
            'parent_id'  => $root2->id,
            'sort_index' => 1,
        ]);
    }
}