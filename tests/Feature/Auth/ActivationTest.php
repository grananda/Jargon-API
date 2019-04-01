<?php


namespace Tests\Feature\Auth;


use App\Events\User\UserWasActivated;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ActivationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_is_returned_when_on_invalid_token()
    {
        // Given
        Event::fake(UserWasActivated::class);

        /** @var \App\Models\User $user */
        $user = $this->user('registered-user', ['activated_at' => null]);

        // When
        $response = $this->get(route('auth.activate', [
            'id'    => $user->uuid,
            'token' => 123,
        ]));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        Event::assertNotDispatched(UserWasActivated::class);
    }

    /** @test */
    public function a_404_is_returned_when_on_invalid_user()
    {
        // Given
        Event::fake(UserWasActivated::class);

        /** @var \App\Models\User $user */
        $user = $this->user('registered-user', ['activated_at' => null]);

        // When
        $response = $this->get(route('auth.activate', [
            'id'    => 123,
            'token' => $user->activation_token,
        ]));

        // Then
        $response->assertStatus(Response::HTTP_NOT_FOUND);

        Event::assertNotDispatched(UserWasActivated::class);
    }

    /** @test */
    public function a_500_is_returned_when_a_registered_user_is_activated_twice()
    {
        // Given
        Event::fake(UserWasActivated::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        // When
        $response = $this->get(route('auth.activate', [
            'id'    => $user->uuid,
            'token' => $user->activation_token,
        ]));

        // Then
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);

        Event::assertNotDispatched(UserWasActivated::class);
    }

    /** @test */
    public function a_500_is_returned_when_a_token_is_expired()
    {
        // Given
        Event::fake(UserWasActivated::class);

        /** @var \App\Models\User $user */
        $user = $this->user('registered-user', [
            'activated_at' => Carbon::now()->subHours(User::ACTIVATION_EXPIRES_AT),
        ]);

        // When
        $response = $this->get(route('auth.activate', [
            'id'    => $user->uuid,
            'token' => $user->activation_token,
        ]));

        // Then
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);

        Event::assertNotDispatched(UserWasActivated::class);
    }

    /** @test */
    public function a_200_is_returned_when_a_registered_user_is_activated()
    {
        // Given
        Event::fake(UserWasActivated::class);

        /** @var \App\Models\User $user */
        $user = $this->user('registered-user', ['activated_at' => null]);

        // When
        $response = $this->get(route('auth.activate', [
            'id'    => $user->uuid,
            'token' => $user->activation_token,
        ]));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotNull($user->fresh()->activated_at);

        Event::assertDispatched(UserWasActivated::class);
    }
}