<?php

namespace Tests\Feature\Unit\Rules;

use App\Models\Organization;
use App\Models\User;
use App\Rules\ValidMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * @group unit
 * @coversNothing
 */
class ValidateMemberTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function run_validator()
    {
        /** @var \App\Models\User $user */
        $user = $this->user();

        $this->assertTrue($this->validator([
            'collaborators' => [
                    'id'    => $user->uuid,
                    'role'  => Organization::ORGANIZATION_DEFAULT_ROLE_ALIAS,
                    'owner' => false,
                ],
        ])->passes());

        $this->assertFalse($this->validator([
            'collaborators' => [
                    'id'    => $user->id,
                    'role'  => Organization::ORGANIZATION_DEFAULT_ROLE_ALIAS,
                    'owner' => false,
                ],
        ])->passes());
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
