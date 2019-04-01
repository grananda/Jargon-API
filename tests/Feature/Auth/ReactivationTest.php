<?php


namespace Tests\Feature\Auth;


use App\Events\User\UserActivationTokenGenerated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReactivationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_404_will_returned_when_a_new_validation_token_is_requested_for_an_invalid_user_email()
    {
        // Given

        /** @var \App\Models\User $user */
        $user = $this->user();

        Event::fake(UserActivationTokenGenerated::class);

        // When
        $response = $this->post(route('auth.activate.resend'), [
            'email' => $this->faker->email,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_NOT_FOUND);

        Event::assertNotDispatched(UserActivationTokenGenerated::class);
    }

    /** @test */
    public function a_403_will_returned_when_a_new_validation_token_is_requested_for_a_validated_user()
    {
        // Given

        /** @var \App\Models\User $user */
        $user = $this->user();

        Event::fake(UserActivationTokenGenerated::class);

        // When
        $response = $this->post(route('auth.activate.resend'), [
            'email' => $user->email,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        Event::assertNotDispatched(UserActivationTokenGenerated::class);
    }

    /** @test */
    public function a_204_will_returned_when_a_new_validation_token_is_sent()
    {
        // Given
        Event::fake(UserActivationTokenGenerated::class);

        $activation_token = Str::random(32);

        /** @var \App\Models\User $user */
        $user = $this->user('registered-user', [
            'activated_at'     => null,
            'activation_token' => $activation_token,
        ]);

        // When
        $response = $this->post(route('auth.activate.resend'), [
            'email' => $user->email,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertNotEquals($user->fresh()->actiovation_token, $activation_token);

        Event::assertDispatched(UserActivationTokenGenerated::class);
    }
}