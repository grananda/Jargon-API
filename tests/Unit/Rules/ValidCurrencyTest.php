<?php


namespace Tests\Unit\Rules;

use App\Rules\ValidCurrencyCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ValidCurrencyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function run_validator()
    {
        $this->assertTrue($this->validator(['currency' => 'EUR'])->passes());
        $this->assertFalse($this->validator(['currency' => 'XXX'])->passes());
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
        $rules = ['currency' => ['required', new ValidCurrencyCode()]];

        return Validator::make($data, $rules);
    }
}