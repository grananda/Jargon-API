<?php

namespace Tests\Feature\Unit\Rules;

use App\Models\Organization;
use App\Models\User;
use App\Rules\ValidMember;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidateMemberTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function run_validator()
    {
        /** @var \App\Models\User $user */
        $user = $this->user();

        $this->assertTrue($this->validator(['collaborators' => [$user->uuid, Organization::ORGANIZATION_DEFAULT_ROLE_ALIAS]])->passes());
        $this->assertFalse($this->validator(['collaborators' => [$user->id, Organization::ORGANIZATION_DEFAULT_ROLE_ALIAS]])->passes());
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
        $rules = ['collaborators' => ['array', new ValidMember()]];

        return Validator::make($data, $rules);
    }
}
