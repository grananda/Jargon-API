<?php

namespace Tests\Feature\User;

use App\Events\User\UserWasDeactivated;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * @group feature
 * @covers \App\Http\Controllers\User\AccountController::deactivate
 */
class DeactivationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_when_a_non_staff_user_deactivates_a_user()
    {
        // Given
        Event::fake(UserWasDeactivated::class);

        /** @var User $staff */
        $staff = $this->user();

        /** @var \App\Models\User $user */
        $user = $this->user('registered-user', [
            'activated_at' => null,
        ]);

        // When
        $response = $this->signIn($staff)->post(route('account.deactivate'), [
            'id' => $user->uuid,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        Event::assertNotDispatched(UserWasDeactivated::class);
    }

    /** @test */
    public function a_403_will_be_returned_when_a_non_senior_staff_user_deactivates_a_user()
    {
        // Given
        Event::fake(UserWasDeactivated::class);

        /** @var User $staff */
        $staff = $this->staff(User::JUNIOR_STAFF_MEMBER);

        /** @var \App\Models\User $user */
        $user = $this->user('registered-user', [
            'activated_at' => null,
        ]);

        // When
        $response = $this->signIn($staff)->post(route('account.deactivate'), [
            'id' => $user->uuid,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        Event::assertNotDispatched(UserWasDeactivated::class);
    }

    /** @test */
    public function a_500_will_be_returned_when_deactivating_a_non_active_user()
    {
        // Given
        Event::fake(UserWasDeactivated::class);

        /** @var User $staff */
        $staff = $this->staff(User::SENIOR_STAFF_MEMBER);

        /** @var \App\Models\User $user */
        $user = $this->user('registered-user', [
            'activated_at' => null,
        ]);

        // When
        $response = $this->signIn($staff)->post(route('account.deactivate'), [
            'id' => $user->uuid,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);

        Event::assertNotDispatched(UserWasDeactivated::class);
    }

    /** @test */
    public function a_200_will_be_returned_when_deactivating_a_user()
    {
        // Given
        Event::fake(UserWasDeactivated::class);

        /** @var User $staff */
        $staff = $this->staff(User::SENIOR_STAFF_MEMBER);

        /** @var \App\Models\User $user */
        $user = $this->user();

        // When
        $response = $this->signIn($staff)->post(route('account.deactivate'), [
            'id' => $user->uuid,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_NO_CONTENT);

        Event::assertDispatched(UserWasDeactivated::class);
    }
}
