<?php

namespace Tests\Feature\Node;

use App\Models\Organization;
use App\Models\Translations\Node;
use App\Models\Translations\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * Class StoreTest
 *
 * @package Tests\Feature\Node
 */
class StoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->post(route('nodes.store'), []);

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_when_storing_a_node_from_an_non_member_project()
    {
        // Given
        /** @var \App\Models\User $user1 */
        $user1 = $this->user();

        /** @var \App\Models\User $user2 */
        $user2 = $this->user();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($user1);

        /** @var \App\Models\Translations\Node $parent */
        $parent = Node::create([
            'key' => $this->faker->word,
        ]);

        $data = [
            'project' => $project->uuid,
            'parent'  => $parent->uuid,
        ];

        // When
        $response = $this->signIn($user2)->post(route('nodes.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_422_will_be_returned_when_storing_a_node_to_a_parent_of_a_non_member_project()
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

        /** @var \App\Models\Translations\Project $project2 */
        $project2 = factory(Project::class)->create();
        $project2->setOrganization($organization);
        $project2->setOwner($user);

        /** @var \App\Models\Translations\Node $parent */
        $parent = Node::create([
            'key' => $this->faker->word,
        ]);
        $project->nodes()->save($parent);

        $data = [
            'project' => $project2->uuid,
            'parent'  => $parent->uuid,
        ];

        // When
        $response = $this->signIn($user)->post(route('nodes.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function a_200_will_be_returned_when_storing_a_node_for_a_project()
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

        $parentKey = 'parentNode';

        /** @var \App\Models\Translations\Node $parent */
        $parent = Node::create([
            'key'        => $parentKey,
            'route'      => $parentKey,
            'project_id' => $project->id,
        ]);

        $node1 = Node::create([
            'key'        => 'node1',
            'route'      => implode('.', [$parentKey, $this->faker->word]),
            'sort_index' => 0,
            'project_id' => $project->id,

        ], $parent);

        Node::create([
            'key'        => 'node1-a',
            'route'      => implode('.', [$node1->key, $this->faker->word]),
            'project_id' => $project->id,
            'sort_index' => 0,
        ], $node1);

        $data = [
            'project' => $project->uuid,
            'parent'  => $parent->uuid,
        ];

        // When
        $response = $this->signIn($user)->post(route('nodes.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_OK);
        $responseData = $response->json()['data'];
        $this->assertDatabaseHas('nodes', [
            'id'         => $responseData['id'],
            'parent_id'  => $parent->id,
            'project_id' => $project->id,
            'sort_index' => 1,
        ]);
        $response->assertJsonFragment([
            'route'      => implode('.', [$parent->key, $responseData['key']]),
            'parent_id'  => $parent->id,
            'project_id' => $project->id,
            'sort_index' => 1,
        ]);
    }

    /** @test */
    public function a_200_will_be_returned_when_storing_a_root_node_for_a_project()
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

        $data = [
            'project' => $project->uuid,
            'parent'  => null,
        ];

        // When
        $response = $this->signIn($user)->post(route('nodes.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_OK);
        $responseData = $response->json()['data'];
        $this->assertDatabaseHas('nodes', [
            'id'         => $responseData['id'],
            'parent_id'  => null,
            'project_id' => $project->id,
            'sort_index' => 1,
        ]);
        $response->assertJsonFragment([
            'route'      => $responseData['key'],
            'parent_id'  => null,
            'project_id' => $project->id,
            'sort_index' => 1,
        ]);
    }
}
