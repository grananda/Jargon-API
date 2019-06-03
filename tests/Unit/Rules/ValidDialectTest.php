<?php

namespace Tests\Unit\Rules;

use App\Models\Dialect;
use App\Rules\ValidDialect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * @group unit
 * @coversNothing
 */
class ValidDialectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function run_validator()
    {
        /** @var \App\Models\Dialect $dialect */
        $dialect = Dialect::inRandomOrder()->first();

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
        $rules = ['dialect' => ['required', new ValidDialect()]];

        return Validator::make($data, $rules);
    }
}
