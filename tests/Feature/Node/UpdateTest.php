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
 * @covers \App\Http\Controllers\Node\NodeController::update
 */
class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->put(route('nodes.update', [123]), []);

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

        $data = [
            'key' => $this->faker->word,
        ];

        // When
        $response = $this->signIn($user)->put(route('nodes.update', [$node->uuid]), $data);

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

        $data = [
            'key' => $this->faker->word,
        ];

        // When
        $response = $this->signIn($user)->put(route('nodes.update', [$node->uuid]), $data);

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_updating_a_node()
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
            'key'        => 'node1',
            'route'      => 'node1',
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        /** @var \App\Models\Translations\Node $node2 */
        $node2 = Node::create([
            'key'        => 'node1',
            'route'      => 'node1.node2',
            'sort_index' => 0,
            'project_id' => $project->id,
        ], $node1);

        /** @var \App\Models\Translations\Node $node3 */
        $node3 = Node::create([
            'key'        => 'node3',
            'route'      => 'node1.node2.node3',
            'sort_index' => 0,
            'project_id' => $project->id,
        ], $node2);

        /** @var \App\Models\Translations\Node $node3 */
        $node3a = Node::create([
            'key'        => 'node3a',
            'route'      => 'node1.node2.node3a',
            'sort_index' => 0,
            'project_id' => $project->id,
        ], $node2);

        /** @var \App\Models\Translations\Node $node3 */
        $node4 = Node::create([
            'key'        => 'node4',
            'route'      => 'node1.node2.node3.node4',
            'sort_index' => 0,
            'project_id' => $project->id,
        ], $node3);
        /** @var \App\Models\Translations\Node $node3 */
        $node4a = Node::create([
            'key'        => 'node4a',
            'route'      => 'node1.node2.node3.node4a',
            'sort_index' => 0,
            'project_id' => $project->id,
        ], $node3);

        $data = [
            'key' => 'new',
        ];

        // When
        $response = $this->signIn($user)->put(route('nodes.update', [$node2->uuid]), $data);

        // Then
        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('nodes', [
            'id'    => $node1->id,
            'key'   => $node1->key,
            'route' => $node1->route,
        ]);
        $this->assertDatabaseHas('nodes', [
            'id'    => $node2->id,
            'key'   => $data['key'],
            'route' => implode('.', [$node1->key, $data['key']]),
        ]);
        $this->assertDatabaseHas('nodes', [
            'id'    => $node3->id,
            'key'   => $node3->key,
            'route' => implode('.', [$node1->key, $data['key'], $node3->key]),
        ]);
        $this->assertDatabaseHas('nodes', [
            'id'    => $node3a->id,
            'key'   => $node3a->key,
            'route' => implode('.', [$node1->key, $data['key'], $node3a->key]),
        ]);
        $this->assertDatabaseHas('nodes', [
            'id'    => $node4->id,
            'key'   => $node4->key,
            'route' => implode('.', [$node1->key, $data['key'], $node3->key, $node4->key]),
        ]);
        $this->assertDatabaseHas('nodes', [
            'id'    => $node4a->id,
            'key'   => $node4a->key,
            'route' => implode('.', [$node1->key, $data['key'], $node3->key, $node4a->key]),
        ]);
    }
}
