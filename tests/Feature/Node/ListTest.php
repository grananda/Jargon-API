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
 * @covers \App\Http\Controllers\Node\NodeController::index
 */
class ListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->get(route('nodes.index', [123]));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_when_listing_all_nodes_from_an_non_member_project()
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

        // When
        $response = $this->signIn($user2)->get(route('nodes.index', [$project->uuid]));

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_listing_all_nodes_from_an_project()
    {
        // Given
        /** @var \App\Models\User $user1 */
        $user1 = $this->user();

        /** @var \App\Models\User $user2 */
        $user2 = $this->user();
        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var \App\Models\Translations\Project $project1 */
        $project1 = factory(Project::class)->create();
        $project1->setOrganization($organization);
        $project1->setOwner($user1);

        /** @var \App\Models\Translations\Node $node1_1 */
        $node1_1 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project1->id,
        ]);

        /** @var \App\Models\Translations\Node $node1_2 */
        $node1_2 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project1->id,
        ], $node1_1);

        /** @var \App\Models\Translations\Project $project2 */
        $project2 = factory(Project::class)->create();
        $project2->setOrganization($organization);
        $project2->setOwner($user2);

        /** @var \App\Models\Translations\Node $node2_1 */
        $node2_1 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project2->id,
        ]);

        /** @var \App\Models\Translations\Project $project3 */
        $project3 = factory(Project::class)->create();
        $project3->setOrganization($organization);
        $project3->setOwner($user1);
        $project3->setMember($user2, Project::PROJECT_DEFAULT_ROLE_ALIAS);

        /** @var \App\Models\Translations\Node $node3_1 */
        $node3_1 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project3->id,
        ]);

        // When
        $response = $this->signIn($user1)->get(route('nodes.index', [$project1->uuid]));

        // Assert
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $node1_1->uuid]);
        $response->assertJsonFragment(['id' => $node1_2->uuid]);
        $response->assertJsonMissing(['id' => $node2_1->uuid]);
        $response->assertJsonMissing(['id' => $node3_1->uuid]);
    }
}
