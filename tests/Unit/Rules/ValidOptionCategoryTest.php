<?php

namespace Tests\Unit\Rules;

use App\Models\Options\OptionCategory;
use App\Rules\ValidOptionCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * @group unit
 * @coversNothing
 */
class ValidOptionCategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function run_validator()
    {
        /** @var OptionCategory $optionCat */
        $optionCat = factory(OptionCategory::class)->create();

        $this->assertTrue($this->validator(['option_category_id' => $optionCat->uuid])->passes());
        $this->assertFalse($this->validator(['option_category_id' => 'abc'])->passes());
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
        $rules = ['option_category_id' => [new ValidOptionCategory()]];

        return Validator::make($data, $rules);
    }
}
