<?php

namespace Tests\Feature\PluginProject;

use App\Models\Dialect;
use App\Models\Organization;
use App\Models\Translations\Project;
use App\Models\User;
use App\Repositories\NodeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @coversNothing
 */
class StoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->post(route('plugin.store', [123]));

        // Then
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_has_no_project_access()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($owner);

        $data = [
            'data' => json_encode([]),
        ];

        // When
        $response = $this->signIn($user)->post(route('plugin.store', [$project->uuid]), $data);

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_processing_remote_translations()
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
            'data' => [
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

        /** @var \App\Repositories\NodeRepository $repo */
        $repo = resolve(NodeRepository::class);

        // When
        $response = $this->signIn($user1)->post(route('plugin.store', [$project1->uuid]), ['data' => $json]);

        // Then
        $response->assertStatus(Response::HTTP_OK);

        $api = $repo->findByOrFail(['project_id' => $project1->id, 'route' => 'api']);
        $this->assertDatabaseHas('nodes', [
            'key'        => 'api',
            'route'      => 'api',
            'project_id' => $project1->id,
            'sort_index' => 1,
            'parent_id'  => null,
        ]);

        $api_messages = $repo->findByOrFail(['project_id' => $project1->id, 'route' => 'api.messages']);
        $this->assertDatabaseHas('nodes', [
            'key'        => 'messages',
            'route'      => 'api.messages',
            'project_id' => $project1->id,
            'sort_index' => 0,
            'parent_id'  => $api->id,
        ]);

        $api_messages_http_ok = $repo->findByOrFail(['project_id' => $project1->id, 'route' => 'api.messages.http_ok']);
        $this->assertDatabaseHas('nodes', [
            'key'        => 'http_ok',
            'route'      => 'api.messages.http_ok',
            'project_id' => $project1->id,
            'sort_index' => 0,
            'parent_id'  => $api_messages->id,
        ]);

        $api_responses = $repo->findByOrFail(['project_id' => $project1->id, 'route' => 'api.responses']);
        $this->assertDatabaseHas('nodes', [
            'key'        => 'responses',
            'route'      => 'api.responses',
            'project_id' => $project1->id,
            'sort_index' => 1,
            'parent_id'  => $api->id,
        ]);

        $api_responses_http_error = $repo->findByOrFail(['project_id' => $project1->id, 'route' => 'api.responses.http_error']);
        $this->assertDatabaseHas('nodes', [
            'key'        => 'http_error',
            'route'      => 'api.responses.http_error',
            'project_id' => $project1->id,
            'sort_index' => 0,
            'parent_id'  => $api_responses->id,
        ]);

        $user = $repo->findByOrFail(['project_id' => $project1->id, 'route' => 'user']);
        $this->assertDatabaseHas('nodes', [
            'key'        => 'user',
            'route'      => 'user',
            'project_id' => $project1->id,
            'sort_index' => 1,
            'parent_id'  => null,
        ]);

        $user_login = $repo->findByOrFail(['project_id' => $project1->id, 'route' => 'user.login']);
        $this->assertDatabaseHas('nodes', [
            'key'        => 'login',
            'route'      => 'user.login',
            'project_id' => $project1->id,
            'sort_index' => 0,
            'parent_id'  => $user->id,
        ]);

        $user_login_message = $repo->findByOrFail(['project_id' => $project1->id, 'route' => 'user.login.message']);
        $this->assertDatabaseHas('nodes', [
            'key'        => 'message',
            'route'      => 'user.login.message',
            'project_id' => $project1->id,
            'sort_index' => 0,
            'parent_id'  => $user_login->id,
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
