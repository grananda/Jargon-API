<?php

namespace Tests\Unit\Services;

use App\Models\Dialect;
use App\Models\Organization;
use App\Models\Translations\Node;
use App\Models\Translations\Project;
use App\Models\Translations\Translation;
use App\Services\Node\NodeTranslationParserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @coversNothing
 */
class NodeTranslationParserServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_translation_file_is_parse_into_a_file_array()
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
            'key'        => 'api',
            'route'      => 'api',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ]);

        /** @var \App\Models\Translations\Node $node1_2 */
        $node1_1 = Node::create([
            'key'        => 'messages',
            'route'      => 'api.messages',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ], $root1);

        /** @var \App\Models\Translations\Node $node1_2 */
        $node1_1_1 = Node::create([
            'key'        => 'http_ok',
            'route'      => 'api.messages.http_ok',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ], $root1);

        $translation1_1_1 = factory(Translation::class)->create([
            'definition' => 'Ok',
            'dialect_id' => $dialect1,
            'node_id'    => $node1_1_1->id,
        ]);

        $translation1_1_2 = factory(Translation::class)->create([
            'dialect_id' => $dialect2,
            'node_id'    => $node1_1_1->id,
        ]);

        /** @var NodeTranslationParserService $service */
        $service = response(NodeTranslationParserService::class);

        $pathPrefix    = 'resources/lang';
        $fileExtension = 'php';
        $fixture       = $this->loadFixture('node/api.php');
        $dataSample    = [
            'locale'  => 'ES',
            'path'    => 'resources/lang/es',
            'file'    => 'file1.php',
            'content' => '<?php return  [];',
            'hash'    => '...',
        ];

        // When
        $response = $service->parseTranslationFile($root1, $dialect1);

        // Then
        $this->assertIsArray($response);
        $this->assertCount(5, $response);

        $this->assertEquals($dialect1->locale, $response['locale']);
        $this->assertEquals("{$pathPrefix}/{$dialect1->locale}", $response['path']);
        $this->assertEquals("{$root1->key}.{$fileExtension}", $response['file']);
        $this->assertArrayHasKey('hash', $response);
        $this->assertEquals($fixture, $response['content']);
    }
}
