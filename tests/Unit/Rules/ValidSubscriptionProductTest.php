<?php


namespace Tests\Unit\Rules;


use App\Models\Subscriptions\SubscriptionProduct;
use App\Rules\ValidSubscriptionProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ValidSubscriptionProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function run_validator()
    {
        /** @var \App\Models\Subscriptions\SubscriptionProduct $product */
        $product = factory(SubscriptionProduct::class)->create();

        $this->assertTrue($this->validator(['product' => $product->uuid])->passes());
        $this->assertFalse($this->validator(['product' => 'XXX'])->passes());
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
        $rules = ['product' => ['required', new ValidSubscriptionProduct()]];

        return Validator::make($data, $rules);
    }
}