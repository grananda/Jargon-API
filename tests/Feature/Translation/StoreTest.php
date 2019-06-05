<?php

namespace Tests\Feature\Translation;

use App\Models\Dialect;
use App\Models\Organization;
use App\Models\Translations\Node;
use App\Models\Translations\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

/**
 * @group feature
 * @covers \App\Http\Controllers\Translations\TranslationController::store
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
        $response = $this->post(route('translations.store'), []);

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_has_no_node_access()
    {
        // Given
        /** @var \App\Models\User $user1 */
        $user1 = $this->user();

        /** @var \App\Models\User $user2 */
        $user2 = $this->user();

        $this->createActiveSubscription($user1, 'professional-month-eur');

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($user1);

        /** @var \App\Models\Translations\Node $node1_1 */
        $node = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        $data = [
            'definition' => $this->faker->paragraph,
            'dialect'    => Dialect::inRandomOrder()->first()->uuid,
            'node'       => $node->uuid,
        ];

        // When
        $response = $this->signIn($user2)->post(route('translations.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_has_no_role_node_access()
    {
        // Given
        /** @var \App\Models\User $user1 */
        $user1 = $this->user();

        /** @var \App\Models\User $user2 */
        $user2 = $this->user();

        $this->createActiveSubscription($user1, 'professional-month-eur');

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($user1);
        $project->setMember($user2, Project::PROJECT_TRANSLATOR_ROLE_ALIAS);

        /** @var \App\Models\Translations\Node $node1_1 */
        $node = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        $data = [
            'definition' => $this->faker->paragraph,
            'dialect'    => Dialect::inRandomOrder()->first()->uuid,
            'node'       => $node->uuid,
        ];

        // When
        $response = $this->signIn($user2)->post(route('translations.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_when_storing_a_translation_to_a_parent_of_a_non_member_node()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        $this->createActiveSubscription($user, 'professional-month-eur');

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($user);

        /** @var \App\Models\Translations\Project $project2 */
        $project2 = factory(Project::class)->create();
        $project2->setOrganization($organization);

        /** @var \App\Models\Translations\Node $node1 */
        $node = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        /** @var \App\Models\Translations\Node $node2 */
        $node2 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project2->id,
        ]);

        $data = [
            'definition' => $this->faker->paragraph,
            'dialect'    => Dialect::inRandomOrder()->first()->uuid,
            'node'       => $node2->uuid,
        ];

        // When
        $response = $this->signIn($user)->post(route('translations.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_creates_a_new_translation_without_translation_quota()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        $this->createActiveSubscription(
            $user,
            'professional-month-eur',
            ['translation_count' => 0]);

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($user);

        /** @var \App\Models\Translations\Node $node1 */
        $node = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        $data = [
            'definition' => $this->faker->paragraph,
            'dialect'    => 123,
            'node'       => $node->uuid,
        ];

        // When
        $response = $this->signIn($user)->post(route('translations.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_422_will_be_returned_when_storing_a_translation_with_a_non_valid_dialect()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        $this->createActiveSubscription($user, 'professional-month-eur');

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($user);

        /** @var \App\Models\Translations\Node $node1 */
        $node = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        $data = [
            'definition' => $this->faker->paragraph,
            'dialect'    => 123,
            'node'       => $node->uuid,
        ];

        // When
        $response = $this->signIn($user)->post(route('translations.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function a_422_will_be_returned_when_storing_a_translation_with_dialect_not_in_project()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        $this->createActiveSubscription($user, 'professional-month-eur');

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var \App\Models\Dialect $dialect */
        $dialect = Dialect::where('locale', 'es_MX')->first();

        /** @var \App\Models\Dialect $dialect */
        $dialect2 = Dialect::where('locale', 'es_ES')->first();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($user);
        $project->dialects()->save($dialect);

        /** @var \App\Models\Translations\Node $node1 */
        $node = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        $data = [
            'definition' => $this->faker->paragraph,
            'dialect'    => $dialect2->locale,
            'node'       => $node->uuid,
        ];

        // When
        $response = $this->signIn($user)->post(route('translations.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function a_200_will_be_returned_when_storing_a_translation()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        $this->createActiveSubscription($user, 'professional-month-eur');

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var \App\Models\Dialect $dialect */
        $dialect = Dialect::where('locale', 'es_MX')->first();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOrganization($organization);
        $project->setOwner($user);
        $project->dialects()->save($dialect);

        /** @var \App\Models\Translations\Node $node1 */
        $node = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        $data = [
            'definition' => $this->faker->paragraph,
            'dialect'    => $dialect->locale,
            'node'       => $node->uuid,
        ];

        // When
        $response = $this->signIn($user)->post(route('translations.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_OK);
        $responseData = $response->json()['data'];

        $this->assertDatabaseHas('translations', [
            'uuid'       => $responseData['id'],
            'node_id'    => $node->id,
            'dialect_id' => $dialect->id,
        ]);

        $response->assertJsonFragment([
            'id' => $responseData['id'],
        ]);
    }
}
