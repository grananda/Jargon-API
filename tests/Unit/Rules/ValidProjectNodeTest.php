<?php

namespace Tests\Unit\Rules;

use App\Models\Translations\Node;
use App\Models\Translations\Project;
use App\Rules\ValidProjectNode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * @group unit
 * @coversNothing
 */
class ValidProjectNodeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function run_validator()
    {
        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();

        /** @var \App\Models\Translations\Project $project2 */
        $project2 = factory(Project::class)->create();

        /** @var \App\Models\Translations\Node $node */
        $node = Node::create([
            'key' => $this->faker->word,
        ]);

        $project->nodes()->save($node);

        $this->assertTrue($this->validator(['node' => $node->uuid], $project)->passes());
        $this->assertFalse($this->validator(['node' => $node->uuid], $project2)->passes());
    }

    /**
     * Construct validator from data.
     *
     * @param array $data
     * @param       $project
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, $project)
    {
        $rules = ['node' => ['required', 'string', new ValidProjectNode($project)]];

        return Validator::make($data, $rules);
    }
}
