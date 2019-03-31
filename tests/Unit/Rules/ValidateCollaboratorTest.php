<?php

namespace Tests\Feature\Unit\Rules;

use App\Models\User;
use App\Rules\ValidCollaborator;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidateCollaboratorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function run_validator()
    {
        /** @var \App\Models\User $user */
        $user = $this->user();

        $this->assertTrue($this->validator(['collaborators' => $user->uuid])->passes());
        $this->assertFalse($this->validator(['collaborators' => $user->id])->passes());
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
        $rules = ['collaborators' => ['string', new ValidCollaborator()]];

        return Validator::make($data, $rules);
    }
}
