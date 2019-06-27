<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CreateProjectPullRequest;
use App\Models\Translations\Project;
use App\Services\Project\ProjectTranslationParserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * @coversNothing
 */
class CreatePullRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function job_gets_dispatched_with_valid_data()
    {
        // Given
        Bus::fake(CreateProjectPullRequest::class);

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();

        $this->mock(ProjectTranslationParserService::class, function ($mock) use ($project) {
            /* @var \Mockery\Mock $mock */
            $mock->shouldReceive('parseProjectTranslationTree')
                ->withAnyArgs()
                ->andReturn([])
            ;
        });

        // When
        CreateProjectPullRequest::dispatch($project);

        // Then
        Bus::assertDispatched(CreateProjectPullRequest::class);
    }
}
