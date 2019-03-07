<?php

namespace Tests\Feature\Unit\Rules;

use App\Models\Organization;
use App\Rules\ValidOrganization;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidateOrganizationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function run_validator()
    {
        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();

        $this->assertTrue($this->validator(['organization' => $organization->uuid])->passes());
        $this->assertFalse($this->validator(['organization' => $organization->id])->passes());
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
        $rules = ['organization' => ['string', new ValidOrganization()]];

        return Validator::make($data, $rules);
    }
}
