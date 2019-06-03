<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group unit
 * @coversNothing
 */
class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_non_staff_user_can_be_created_and_detected()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        // When
        $response = $user->isStaffMember();

        // Then
        $this->assertFalse($response);
    }

    /** @test */
    public function a_staff_user_can_be_created_and_detected()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->staff();

        // When
        $response = $user->isStaffMember();

        // Then
        $this->assertTrue($response);
    }
}
