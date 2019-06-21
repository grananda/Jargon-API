<?php

namespace Tests\External\Services\GitHub;

use App\Models\Translations\GitConfig;
use App\Services\GitHub\GitHubRepoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group  external
 * @covers \App\Services\GitHub\GitHubRepoService
 */
class GitHubRepoServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Services\GitHub\GitHubRepoService
     */
    private $gitHubRepoService;

    /**
     * @var \App\Models\Translations\GitConfig
     */
    private $gitConfig;

    public function setUp(): void
    {
        parent::setUp();

        $this->gitConfig = factory(GitConfig::class)->create(
            [
                'access_token' => env('GIT_HUB_TEST_TOKEN'),
                'username'     => env('GIT_HUB_TEST_USER'),
                'repository'   => env('GIT_HUB_TEST_REPO'),
            ]
        );

        $this->gitHubRepoService = resolve(GitHubRepoService::class);
    }

    /**
     * @test
     *
     * @throws \App\Exceptions\GitHubConnectionException
     */
    public function get_repository_list()
    {
        // When
        $response = $this->gitHubRepoService->getRepositoryList($this->gitConfig);

        // Then
        $this->assertIsArray($response);
        $this->assertSame($this->gitConfig->repository, $response[0]['name']);
    }

    /**
     * @test
     *
     * @throws \App\Exceptions\GitHubConnectionException
     */
    public function repository_information_can_be_retrieved()
    {
        // When
        $response = $this->gitHubRepoService->getRepositoryDetails($this->gitConfig, $this->gitConfig->repository);

        // Then
        $this->assertIsArray($response);
        $this->assertSame($this->gitConfig->repository, $response['name']);
    }
}
