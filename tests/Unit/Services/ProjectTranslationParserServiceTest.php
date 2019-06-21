<?php

namespace Tests\Unit\Services;

use App\Models\Dialect;
use App\Models\Organization;
use App\Models\Translations\Node;
use App\Models\Translations\Project;
use App\Models\Translations\Translation;
use App\Services\Project\ProjectTranslationParserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @coversNothing
 */
class ProjectTranslationParserServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_project_is_parse_into_a_translation_array()
    {
        // Given
        $user1 = $this->user();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var \App\Models\Translations\Project $project1 */
        $project1 = factory(Project::class)->create();
        $project1->setOrganization($organization);
        $project1->setOwner($user1);

        $dialect1 = Dialect::where('locale', 'es_ES')->first();

        $dialect2 = Dialect::where('locale', 'en_US')->first();

        $project1->setDialects(
            [
                $dialect1->id => ['is_default' => true],
                $dialect2->id => ['is_default' => false],
            ]
        );

        /** @var \App\Models\Translations\Node $node1_1 */
        $root1 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project1->id,
        ]);

        /** @var \App\Models\Translations\Node $node1_2 */
        $node1_1 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project1->id,
        ], $root1);

        $translation1_1 = factory(Translation::class)->create([
            'dialect_id' => $dialect1,
            'node_id'    => $node1_1->id,
        ]);

        /** @var \App\Models\Translations\Node $node1_2 */
        $node1_2 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project1->id,
        ], $root1);

        /** @var \App\Models\Translations\Node $node1_2 */
        $node1_2_1 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project1->id,
        ], $node1_2);

        $translation1_2_1 = factory(Translation::class)->create([
            'dialect_id' => $dialect1,
            'node_id'    => $node1_2_1->id,
        ]);

        /** @var \App\Models\Translations\Node $node1_1 */
        $root2 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project1->id,
        ]);

        /** @var \App\Models\Translations\Node $node1_2 */
        $node2_1 = Node::create([
            'key'        => $this->faker->word,
            'route'      => $this->faker->word,
            'sort_index' => 0,
            'project_id' => $project1->id,
        ], $root2);

        $translation2_1_1 = factory(Translation::class)->create([
            'dialect_id' => $dialect1,
            'node_id'    => $node2_1->id,
        ]);

        $translation2_1_2 = factory(Translation::class)->create([
            'dialect_id' => $dialect2,
            'node_id'    => $node2_1->id,
        ]);

        /** @var ProjectTranslationParserService $service */
        $service = resolve(ProjectTranslationParserService::class);

        $pathPrefix    = 'resources/lang';
        $fileExtension = 'php';

        $dataSample = [
            [
                [
                    'locale'  => 'ES',
                    'path'    => 'resources/lang/es',
                    'file'    => 'file1.php',
                    'content' => '<?php return  [];',
                    'hash'    => '...',
                ],
                [
                    'locale'  => 'EN',
                    'path'    => 'resources/lang/en',
                    'file'    => 'file1.php',
                    'content' => '<?php return [];',
                    'hash'    => '...',
                ],
            ],
            [
                [
                    'locale'  => 'ES',
                    'path'    => 'resources/lang/es',
                    'file'    => 'file2.php',
                    'content' => '<?php return [];',
                    'hash'    => '...',
                ],
                [
                    'locale'  => 'EN',
                    'path'    => 'resources/lang/en',
                    'file'    => 'file2.php',
                    'content' => '<?php return [];',
                    'hash'    => '...',
                ],
            ],
        ];

        // When
        $response = $service->parseProjectTranslationTree($project1);

        // Then
        $this->assertIsArray($response);
        $this->assertCount(2, $response);

        $this->assertEquals($dialect1->locale, $response[0][0]['locale']);
        $this->assertEquals("{$pathPrefix}/{$dialect1->locale}", $response[0][0]['path']);
        $this->assertEquals("{$root1->key}.{$fileExtension}", $response[0][0]['file']);
        $this->assertArrayHasKey('hash', $response[0][0]);
        $this->assertStringContainsString($root1->key, $response[0][0]['content']);
        $this->assertStringContainsString($node1_1->key, $response[0][0]['content']);
        $this->assertStringContainsString($node1_2->key, $response[0][0]['content']);
        $this->assertStringContainsString($node1_2_1->key, $response[0][0]['content']);
        $this->assertStringContainsString($translation1_1->definition, $response[0][0]['content']);
        $this->assertStringContainsString($translation1_2_1->definition, $response[0][0]['content']);

        $this->assertEquals($dialect2->locale, $response[0][1]['locale']);
        $this->assertEquals("{$pathPrefix}/{$dialect2->locale}", $response[0][1]['path']);
        $this->assertEquals("{$root1->key}.{$fileExtension}", $response[0][1]['file']);
        $this->assertArrayHasKey('hash', $response[0][1]);
        $this->assertStringContainsString($root1->key, $response[0][1]['content']);
        $this->assertStringContainsString($node1_1->key, $response[0][1]['content']);
        $this->assertStringContainsString($node1_2->key, $response[0][1]['content']);
        $this->assertStringContainsString($node1_2_1->key, $response[0][1]['content']);
        $this->assertStringContainsString($translation1_1->definition, $response[0][1]['content']);
        $this->assertStringContainsString($translation1_2_1->definition, $response[0][1]['content']);

        $this->assertEquals($dialect1->locale, $response[1][0]['locale']);
        $this->assertEquals("{$pathPrefix}/{$dialect1->locale}", $response[1][0]['path']);
        $this->assertEquals("{$root2->key}.{$fileExtension}", $response[1][0]['file']);
        $this->assertArrayHasKey('hash', $response[1][0]);
        $this->assertStringContainsString($root2->key, $response[1][0]['content']);
        $this->assertStringContainsString($node2_1->key, $response[1][0]['content']);
        $this->assertStringContainsString($translation2_1_1->definition, $response[1][0]['content']);

        $this->assertEquals($dialect2->locale, $response[1][1]['locale']);
        $this->assertEquals("{$pathPrefix}/{$dialect2->locale}", $response[1][1]['path']);
        $this->assertEquals("{$root2->key}.{$fileExtension}", $response[1][1]['file']);
        $this->assertArrayHasKey('hash', $response[1][1]);
        $this->assertStringContainsString($root2->key, $response[1][1]['content']);
        $this->assertStringContainsString($node2_1->key, $response[1][1]['content']);
        $this->assertStringContainsString($translation2_1_2->definition, $response[1][1]['content']);
    }
}
