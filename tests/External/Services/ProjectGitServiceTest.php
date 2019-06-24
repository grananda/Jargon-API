<?php

namespace Tests\Unit\Services;

use App\Models\Dialect;
use App\Models\Organization;
use App\Models\Translations\GitConfig;
use App\Models\Translations\JargonOption;
use App\Models\Translations\Node;
use App\Models\Translations\Project;
use App\Models\Translations\Translation;
use App\Services\GitHub\GitHubBranchService;
use App\Services\GitHub\GitHubPullRequestService;
use App\Services\Project\ProjectGitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group  external
 * @covers \App\Services\Project\ProjectGitService
 */
class ProjectGitServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test
     * @throws \App\Exceptions\GitHubConnectionException
     * @throws \Github\Exception\MissingArgumentException
     */
    public function a_project_is_process_into_git()
    {
        // Given
        $user1 = $this->user();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();

        /** @var \App\Models\Translations\Project $project1 */
        $project1 = factory(Project::class)->create();
        $project1->setOrganization($organization);
        $project1->setOwner($user1);

        /** @var \App\Models\Translations\GitConfig $gitConfig */
        $gitConfig = factory(GitConfig::class)->create(
            [
                'access_token' => env('GIT_HUB_TEST_TOKEN'),
                'username'     => env('GIT_HUB_TEST_USER'),
                'repository'   => env('GIT_HUB_TEST_REPO'),
                'project_id'   => $project1->id,
            ]
        );

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

        /** @var \App\Models\Translations\Node $root2 */
        $root2 = Node::create([
            'key'        => 'api2',
            'route'      => 'api2',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ]);

        /** @var \App\Models\Translations\Node $node2_1 */
        $node2_1 = Node::create([
            'key'        => 'responses',
            'route'      => 'api2.responses',
            'sort_index' => 1,
            'project_id' => $project1->id,
        ], $root2);

        /** @var \App\Models\Translations\Node $node2_1_1 */
        $node2_1_1 = Node::create([
            'key'        => 'http_error',
            'route'      => 'api2.responses.http_error',
            'sort_index' => 0,
            'project_id' => $project1->id,
        ], $node2_1);

        /** @var \App\Models\Translations\Translation $translation2_1_1 */
        $translation2_1_1 = factory(Translation::class)->create([
            'definition' => 'Error-EN',
            'dialect_id' => $dialect2,
            'node_id'    => $node2_1_1->id,
        ]);

        /** @var ProjectGitService $service */
        $service = resolve(ProjectGitService::class);

        // When
        $response = $service->createPullRequestFromProjectNodes($project1);

        // Then
        $this->assertIsArray($response);

        // Clean
        /** @var GitHubBranchService $gitHubBranchService */
        $gitHubBranchService = resolve(GitHubBranchService::class);

        /** @var GitHubPullRequestService $gitHubPullRequestService */
        $gitHubPullRequestService = resolve(GitHubPullRequestService::class);

        $gitHubPullRequestService->closePullRequest($gitConfig, $response['number']);
        $gitHubBranchService->removeBranch($gitConfig, $response['branch']);
    }
}
