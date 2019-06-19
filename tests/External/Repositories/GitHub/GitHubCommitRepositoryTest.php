<?php

namespace Tests\External\Repositories\GitHub;

use App\Models\Translations\Project;
use App\Models\Translations\ProjectGitHubConfig;
use App\Repositories\GitHub\GitHubBranchRepository;
use App\Repositories\GitHub\GitHubCommitRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group  external
 * @covers \App\Repositories\GitHub\GitHubCommitRepository
 */
class GitHubCommitRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Models\Translations\Project
     */
    private $project;

    /**
     * @var \App\Repositories\GitHub\GitHubBranchRepository
     */
    private $gitHubBranchRepository;

    /**
     * @var \App\Repositories\GitHub\GitHubCommitRepository
     */
    private $gitHubCommitRepository;

    /**
     * @var \App\Models\Translations\ProjectGitHubConfig
     */
    private $gitConfig;

    public function setUp(): void
    {
        parent::setUp();

        $user = $this->user();

        /* @var \App\Models\Translations\Project $project */
        $this->project = factory(Project::class)->create();
        $this->project->setOwner($user);

        $this->gitConfig = factory(ProjectGitHubConfig::class)->create(
            [
                'access_token' => env('GIT_HUB_TEST_TOKEN'),
                'username'     => env('GIT_HUB_TEST_USER'),
                'repository'   => env('GIT_HUB_TEST_REPO'),
                'project_id'   => $this->project->id,
            ]
        );

        $this->gitHubBranchRepository = resolve(GitHubBranchRepository::class);

        $this->gitHubCommitRepository = resolve(GitHubCommitRepository::class);
    }

    /** @test */
    public function a_new_file_can_be_committed_into_a_branch()
    {
        // Given
        $branchName = 'featureTest';

        // Step 1
        $branch    = $this->gitHubBranchRepository->createBranch($this->project, $branchName);
        $branchSha = $branch['object']['sha'];

        // When
        $response = $this->gitHubCommitRepository->commitFiles(
            [
                'username'   => $this->project->gitHubConfig->username,
                'email'      => 'jfernandez74@gmail.com',
                'repository' => $this->project->gitHubConfig->repository,
                'branch'     => $branchName,
                'sha'        => $branchSha,
                'files'      => [
                    [
                        'path'    => 'test1.php',
                        'mode'    => '100644',
                        'type'    => 'blob',
                        'content' => '<?php [];',
                    ],
                    [
                        'path'    => 'test2.php',
                        'mode'    => '100644',
                        'type'    => 'blob',
                        'content' => '<?php [];',
                    ],
                    [
                        'path'    => 'test3.php',
                        'mode'    => '100644',
                        'type'    => 'blob',
                        'content' => '<?php [];',
                    ],
                ],
            ]
        );

        // Then
        $this->assertSame("refs/heads/{$branchName}", $response['ref']);
        $this->assertSame('commit', $response['object']['type']);

        // Clean
        $this->gitHubBranchRepository->removeBranch($this->project, $branchName, $branchSha);
    }
}
