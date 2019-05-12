<?php

namespace Tests\Feature\Translation;

use App\Models\Organization;
use App\Models\Translations\Node;
use App\Models\Translations\Project;
use App\Models\Translations\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * Class ListTest.
 *
 * @package Tests\Feature\Node
 * @coversNothing
 */
class ListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->get(route('translations.index', [123]));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_when_listing_all_translations_from_an_non_member_project()
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
    public function a_200_will_be_returned_when_listing_all_translations_from_an_node()
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

        /** @var \App\Models\Translations\Node $node1 */
        $node1 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        /** @var \App\Models\Translations\Node $node1_2 */
        $node2 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ], $node1);

        /** @var Translation $translation1 */
        $translation1 = factory(Translation::class)->create(['node_id' => $node1->id]);

        /** @var Translation $tranalation1 */
        $translation2 = factory(Translation::class)->create(['node_id' => $node2->id]);

        // When
        $response = $this->signIn($user1)->get(route('translations.index', [$node1->uuid]));

        // Assert
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $translation1->uuid]);
        $response->assertJsonMissing(['id' => $translation2->uuid]);
    }
}
