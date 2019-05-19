<?php

namespace Tests\Feature\Translation;

use App\Models\Dialect;
use App\Models\Organization;
use App\Models\Translations\Node;
use App\Models\Translations\Project;
use App\Models\Translations\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

/**
 * @group feature
 * @coversNothing
 */
class UpdateTest extends TestCase
{
    use RefreshDatabase,
        CreateActiveSubscription;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->put(route('translations.update', [123]), []);

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_has_no_node_access()
    {
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

        /** @var \App\Models\Translations\Translation $translation */
        $translation = factory(Translation::class)->create([
            'definition' => $this->faker->paragraph,
            'dialect_id' => Dialect::inRandomOrder()->first()->uuid,
            'node_id'    => $node->id,
        ]);

        $data = [
            'definition' => $this->faker->paragraph,
        ];

        // When
        $response = $this->signIn($user2)->put(route('translations.update', [$translation->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_storing_a_translation_to_a_different_node()
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

        /** @var \App\Models\Translations\Translation $translation */
        $translation = factory(Translation::class)->create([
            'definition' => $this->faker->paragraph,
            'dialect_id' => Dialect::inRandomOrder()->first()->id,
            'node_id'    => $node->id,
        ]);

        $data = [
            'definition' => $this->faker->paragraph,
            'node_id'    => $node2->uuid,
        ];

        // When
        $response = $this->signIn($user)->put(route('translations.update', [$translation->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('translations', [
            'uuid'       => $translation->uuid,
            'node_id'    => $node->id,
            'dialect_id' => $translation->dialect->id,
            'definition' => $data['definition'],
        ]);
    }

    /** @test */
    public function a_200_will_be_returned_when_storing_a_translation_to_a_different_dialect()
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

        /** @var \App\Models\Dialect $dialect */
        $dialect = Dialect::where('locale', 'es_ES')->first();

        /** @var \App\Models\Translations\Translation $translation */
        $translation = factory(Translation::class)->create([
            'definition' => $this->faker->paragraph,
            'dialect_id' => $dialect->id,
            'node_id'    => $node->id,
        ]);

        $data = [
            'definition' => $this->faker->paragraph,
            'dialect'    => Dialect::where('locale', 'es_MX')->first()->uuid,
        ];

        // When
        $response = $this->signIn($user)->put(route('translations.update', [$translation->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('translations', [
            'uuid'       => $translation->uuid,
            'node_id'    => $node->id,
            'dialect_id' => $dialect->id,
            'definition' => $data['definition'],
        ]);
    }

    /** @test */
    public function a_200_will_be_returned_if_the_user_has_low_role_node_access()
    {
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
        $project->validateMember($user2);

        /** @var \App\Models\Translations\Node $node1_1 */
        $node = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project->id,
        ]);

        /** @var \App\Models\Translations\Translation $translation */
        $translation = factory(Translation::class)->create([
            'definition' => $this->faker->paragraph,
            'dialect_id' => Dialect::inRandomOrder()->first()->id,
            'node_id'    => $node->id,
        ]);

        $data = [
            'definition' => $this->faker->paragraph,
        ];

        // When
        $response = $this->signIn($user2)->put(route('translations.update', [$translation->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_OK);
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

        /** @var \App\Models\Translations\Translation $translation */
        $translation = factory(Translation::class)->create([
            'definition' => $this->faker->paragraph,
            'dialect_id' => $dialect->id,
            'node_id'    => $node->id,
        ]);

        $data = [
            'definition' => $this->faker->paragraph,
        ];

        // When
        $response = $this->signIn($user)->put(route('translations.update', [$translation->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_OK);
        $responseData = $response->json()['data'];

        $this->assertDatabaseHas('translations', [
            'uuid'       => $translation->uuid,
            'node_id'    => $node->id,
            'dialect_id' => $dialect->id,
            'definition' => $data['definition'],
        ]);

        $response->assertJsonFragment([
            'id' => $responseData['id'],
        ]);
    }
}
