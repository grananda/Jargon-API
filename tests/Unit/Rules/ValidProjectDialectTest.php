<?php

namespace Tests\Unit\Rules;

use App\Models\Dialect;
use App\Models\Translations\Project;
use App\Rules\ValidProjectDialect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * @coversNothing
 */
class ValidProjectDialectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function run_validator()
    {
        /** @var Dialect $dialect */
        $dialect = Dialect::where('locale', 'es_ES')->first();

        $this->assertTrue($this->validator(['dialect' => $dialect->locale])->passes());
        $this->assertFalse($this->validator(['dialect' => 'XXX'])->passes());
    }

    /**
     * Construct validator from data.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data)
    {
        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();

        /** @var Dialect $dialect */
        $dialect = Dialect::where('locale', 'es_ES')->first();
        $project->dialects()->save($dialect);

        $rules = ['dialect' => ['required', new ValidProjectDialect($project)]];

        return Validator::make($data, $rules);
    }
}
