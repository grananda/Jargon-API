<?php

namespace Tests\Unit\Services;

use App\Models\Dialect;
use App\Models\Organization;
use App\Models\Translations\JargonOption;
use App\Models\Translations\Node;
use App\Models\Translations\Project;
use App\Models\Translations\Translation;
use App\Services\Node\NodeTranslationParserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group  unit
 * @covers \App\Services\Node\NodeTranslationParserService
 */
class NodeTranslationParserServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_array_is_built_from_a_root_node()
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

        /** @var \App\Models\Translations\Node $root1 */
        $root1 = Node::create([
            'key'        => 'api',
            'route'      => 'api',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ]);

        /** @var \App\Models\Translations\Node $node1_1 */
        $node1_1 = Node::create([
            'key'        => 'messages',
            'route'      => 'api.messages',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ], $root1);

        /** @var \App\Models\Translations\Node $node1_1_1 */
        $node1_1_1 = Node::create([
            'key'        => 'http_ok',
            'route'      => 'api.messages.http_ok',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ], $node1_1);

        /** @var \App\Models\Translations\Translation $translation1_1_1 */
        $translation1_1_1 = factory(Translation::class)->create([
            'definition' => 'Ok-ES',
            'dialect_id' => $dialect1,
            'node_id'    => $node1_1_1->id,
        ]);

        /** @var \App\Models\Translations\Translation $translation1_1_2 */
        $translation1_1_2 = factory(Translation::class)->create([
            'definition' => 'Ok-EN',
            'dialect_id' => $dialect2,
            'node_id'    => $node1_1_1->id,
        ]);

        /** @var \App\Models\Translations\Node $node2_1 */
        $node2_1 = Node::create([
            'key'        => 'responses',
            'route'      => 'api.responses',
            'sort_index' => 1,
            'project_id' => $project1->id,
        ], $root1);

        /** @var \App\Models\Translations\Node $node2_1_1 */
        $node2_1_1 = Node::create([
            'key'        => 'http_error',
            'route'      => 'api.responses.http_error',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ], $node2_1);

        /** @var \App\Models\Translations\Translation $translation2_1_1 */
        $translation2_1_1 = factory(Translation::class)->create([
            'definition' => 'Error-ES',
            'dialect_id' => $dialect1,
            'node_id'    => $node2_1_1->id,
        ]);

        /** @var \App\Models\Translations\Translation $translation2_1_2 */
        $translation2_1_2 = factory(Translation::class)->create([
            'definition' => 'Error-EN',
            'dialect_id' => $dialect2,
            'node_id'    => $node2_1_1->id,
        ]);

        /** @var \App\Services\Node\NodeTranslationParserService $service */
        $service = resolve(NodeTranslationParserService::class);

        $fixture = [
            'api' => [
                'messages' => [
                    'http_ok' => 'Ok-ES',
                ],
                'responses' => [
                    'http_error' => 'Error-ES',
                ],
            ],
        ];

        // When
        $response = $service->treeToArray($root1, $dialect1, $dialect1);

        // Then
        $this->assertIsArray($response);
        $this->assertSame($fixture, $response);
    }

    /** @test */
    public function a_default_translation_is_returned_when_requested_unavailable()
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

        /** @var \App\Models\Translations\Node $root1 */
        $root1 = Node::create([
            'key'        => 'api',
            'route'      => 'api',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ]);

        /** @var \App\Models\Translations\Node $node1_1 */
        $node1_1 = Node::create([
            'key'        => 'messages',
            'route'      => 'api.messages',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ], $root1);

        /** @var \App\Models\Translations\Node $node1_1_1 */
        $node1_1_1 = Node::create([
            'key'        => 'http_ok',
            'route'      => 'api.messages.http_ok',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ], $node1_1);

        /** @var \App\Models\Translations\Translation $translation1_1_1 */
        $translation1_1_1 = factory(Translation::class)->create([
            'definition' => 'Ok-ES',
            'dialect_id' => $dialect1,
            'node_id'    => $node1_1_1->id,
        ]);

        /** @var \App\Services\Node\NodeTranslationParserService $service */
        $service = resolve(NodeTranslationParserService::class);

        // When
        $response = $service->treeToArray($root1, $dialect2, $dialect1);

        // Then
        $this->assertIsArray($response);
        $this->assertStringContainsString($translation1_1_1->definition, serialize($response));
    }

    /** @test */
    public function a_json_is_built_from_a_root_node()
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

        /** @var \App\Models\Translations\Node $root1 */
        $root1 = Node::create([
            'key'        => 'api',
            'route'      => 'api',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ]);

        /** @var \App\Models\Translations\Node $node1_1 */
        $node1_1 = Node::create([
            'key'        => 'messages',
            'route'      => 'api.messages',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ], $root1);

        /** @var \App\Models\Translations\Node $node1_1_1 */
        $node1_1_1 = Node::create([
            'key'        => 'http_ok',
            'route'      => 'api.messages.http_ok',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ], $node1_1);

        /** @var \App\Models\Translations\Translation $translation1_1_1 */
        $translation1_1_1 = factory(Translation::class)->create([
            'definition' => 'Ok-ES',
            'dialect_id' => $dialect1,
            'node_id'    => $node1_1_1->id,
        ]);

        /** @var \App\Models\Translations\Translation $translation1_1_2 */
        $translation1_1_2 = factory(Translation::class)->create([
            'definition' => 'Ok-EN',
            'dialect_id' => $dialect2,
            'node_id'    => $node1_1_1->id,
        ]);

        /** @var \App\Models\Translations\Node $node2_1 */
        $node2_1 = Node::create([
            'key'        => 'responses',
            'route'      => 'api.responses',
            'sort_index' => 1,
            'project_id' => $project1->id,
        ], $root1);

        /** @var \App\Models\Translations\Node $node2_1_1 */
        $node2_1_1 = Node::create([
            'key'        => 'http_error',
            'route'      => 'api.responses.http_error',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ], $node2_1);

        /** @var \App\Models\Translations\Translation $translation2_1_1 */
        $translation2_1_1 = factory(Translation::class)->create([
            'definition' => 'Error-ES',
            'dialect_id' => $dialect1,
            'node_id'    => $node2_1_1->id,
        ]);

        /** @var \App\Models\Translations\Translation $translation2_1_2 */
        $translation2_1_2 = factory(Translation::class)->create([
            'definition' => 'Error-EN',
            'dialect_id' => $dialect2,
            'node_id'    => $node2_1_1->id,
        ]);

        /** @var \App\Services\Node\NodeTranslationParserService $service */
        $service = resolve(NodeTranslationParserService::class);

        $fixture = json_encode([
            'api' => [
                'messages' => [
                    'http_ok' => 'Ok-ES',
                ],
                'responses' => [
                    'http_error' => 'Error-ES',
                ],
            ],
        ]);

        // When
        $response = $service->treeToJson($root1, $dialect1, $dialect1);

        // Then
        $this->assertSame($fixture, $response);
    }

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

        /** @var JargonOption $options */
        $options = factory(JargonOption::class)->create([
            'project_id' => $project1->id,
        ]);

        /** @var \App\Models\Translations\Node $root1 */
        $root1 = Node::create([
            'key'        => 'api',
            'route'      => 'api',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ]);

        /** @var \App\Models\Translations\Node $node1_1 */
        $node1_1 = Node::create([
            'key'        => 'messages',
            'route'      => 'api.messages',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ], $root1);

        /** @var \App\Models\Translations\Node $node1_1_1 */
        $node1_1_1 = Node::create([
            'key'        => 'http_ok',
            'route'      => 'api.messages.http_ok',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ], $node1_1);

        /** @var \App\Models\Translations\Translation $translation1_1_1 */
        $translation1_1_1 = factory(Translation::class)->create([
            'definition' => 'Ok-ES',
            'dialect_id' => $dialect1,
            'node_id'    => $node1_1_1->id,
        ]);

        /** @var \App\Models\Translations\Translation $translation1_1_2 */
        $translation1_1_2 = factory(Translation::class)->create([
            'definition' => 'Ok-EN',
            'dialect_id' => $dialect2,
            'node_id'    => $node1_1_1->id,
        ]);

        /** @var \App\Models\Translations\Node $node2_1 */
        $node2_1 = Node::create([
            'key'        => 'responses',
            'route'      => 'api.responses',
            'sort_index' => 1,
            'project_id' => $project1->id,
        ], $root1);

        /** @var \App\Models\Translations\Node $node2_1_1 */
        $node2_1_1 = Node::create([
            'key'        => 'http_error',
            'route'      => 'api.responses.http_error',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ], $node2_1);

        /** @var \App\Models\Translations\Translation $translation2_1_1 */
        $translation2_1_1 = factory(Translation::class)->create([
            'definition' => 'Error-ES',
            'dialect_id' => $dialect1,
            'node_id'    => $node2_1_1->id,
        ]);

        /** @var \App\Models\Translations\Translation $translation2_1_2 */
        $translation2_1_2 = factory(Translation::class)->create([
            'definition' => 'Error-EN',
            'dialect_id' => $dialect2,
            'node_id'    => $node2_1_1->id,
        ]);

        /** @var \App\Services\Node\NodeTranslationParserService $service */
        $service = resolve(NodeTranslationParserService::class);

        $fixture = $this->loadFixture('node/api.php.txt', false);

        // When
        $response = $service->parseTranslationFile($root1, $dialect1, $dialect1, $options);

        // Then
        $this->assertIsArray($response);
        $this->assertCount(5, $response);

        $this->assertEquals($dialect1->locale, $response['locale']);
        $this->assertEquals("{$options->i18n_path}{$dialect1->locale}", $response['path']);
        $this->assertEquals("{$root1->key}.{$options->file_ext}", $response['file']);
        $this->assertArrayHasKey('hash', $response);
        $this->assertEquals($fixture, $response['content']);
    }

    /** @test */
    public function a_node_structure_is_created_from_a_json_string()
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

        /** @var array $data */
        $data = [
            'project' => $project1->uuid,
            'data'    => [
                [
                    [
                        'locale'  => 'es_ES',
                        'content' => [
                            'api' => [
                                'messages' => [
                                    'http_ok' => 'Ok-ES',
                                ],
                                'responses' => [
                                    'http_error' => 'Error-ES',
                                ],
                            ],
                        ],
                        'hash' => $this->faker->sha1,
                    ],
                    [
                        'locale'  => 'en_US',
                        'content' => [
                            'api' => [
                                'messages' => [
                                    'http_ok' => 'Ok-EN',
                                ],
                                'responses' => [
                                    'http_error' => 'Error-EN',
                                ],
                            ],
                        ],
                        'hash' => $this->faker->sha1,
                    ],
                ],
                [
                    [
                        'locale'  => 'es_ES',
                        'content' => [
                            'user' => [
                                'login' => [
                                    'message' => 'acceder',
                                ],
                            ],
                        ],
                        'hash' => $this->faker->sha1,
                    ],
                    [
                        'locale'  => 'en_US',
                        'content' => [
                            'user' => [
                                'login' => [
                                    'message' => 'login',
                                ],
                            ],
                        ],
                        'hash' => $this->faker->sha1,
                    ],
                ],
            ],
        ];

        /** @var string $json */
        $json = json_encode($data);

        /** @var \App\Services\Node\NodeTranslationParserService $service */
        $service = resolve(NodeTranslationParserService::class);

        // When

        $service->jsonToTree($project1, $json);

        // Then
        $this->assertDatabaseHas('nodes', [
            'key'        => 'api',
            'route'      => 'api',
            'project_id' => $project1->id,
            'sort_index' => 1,
            'parent_id'  => null,
        ]);
        $this->assertDatabaseHas('nodes', [
            'key'        => 'messages',
            'route'      => 'api.messages',
            'project_id' => $project1->id,
            'sort_index' => 0,
        ]);
        $this->assertDatabaseHas('nodes', [
            'key'        => 'http_ok',
            'route'      => 'api.messages.http_ok',
            'project_id' => $project1->id,
            'sort_index' => 0,
        ]);
        $this->assertDatabaseHas('nodes', [
            'key'        => 'responses',
            'route'      => 'api.responses',
            'project_id' => $project1->id,
            'sort_index' => 1,
        ]);
        $this->assertDatabaseHas('nodes', [
            'key'        => 'http_error',
            'route'      => 'api.responses.http_error',
            'project_id' => $project1->id,
            'sort_index' => 0,
        ]);

        $this->assertDatabaseHas('nodes', [
            'key'        => 'user',
            'route'      => 'user',
            'project_id' => $project1->id,
            'sort_index' => 1,
            'parent_id'  => null,
        ]);
        $this->assertDatabaseHas('nodes', [
            'key'        => 'login',
            'route'      => 'user.login',
            'project_id' => $project1->id,
            'sort_index' => 0,
        ]);
        $this->assertDatabaseHas('nodes', [
            'key'        => 'message',
            'route'      => 'user.login.message',
            'project_id' => $project1->id,
            'sort_index' => 0,
        ]);

        $this->assertDatabaseHas('translations', [
            'dialect_id' => $dialect1->id,
            'definition' => 'Ok-ES',
        ]);
        $this->assertDatabaseHas('translations', [
            'dialect_id' => $dialect2->id,
            'definition' => 'Ok-EN',
        ]);

        $this->assertDatabaseHas('translations', [
            'dialect_id' => $dialect1->id,
            'definition' => 'acceder',
        ]);
        $this->assertDatabaseHas('translations', [
            'dialect_id' => $dialect2->id,
            'definition' => 'login',
        ]);
    }
}
