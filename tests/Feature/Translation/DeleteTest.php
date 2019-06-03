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
 * @group feature
 * @coversNothing
 */
class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->delete(route('translations.destroy', [123]));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_has_no_translation_access()
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

        /** @var Translation $translation */
        $translation = factory(Translation::class)->create(['node_id' => $node->id]);

        // When
        $response = $this->signIn($user)->delete(route('translations.destroy', [$translation->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_has_no_role_translation_access()
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

        $translation = factory(Translation::class)->create(['node_id' => $node->id]);

        // When
        $response = $this->signIn($user)->delete(route('translations.destroy', [$translation->uuid]));

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

        $translation = factory(Translation::class)->create(['node_id' => $node->id]);

        // When
        $response = $this->signIn($user)->delete(route('translations.destroy', [$translation->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('nodes', [
            'uuid' => $translation->uuid,
        ]);
    }
}
