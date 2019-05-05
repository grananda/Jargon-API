<?php

namespace Tests\Feature\User;

use App\Events\User\PasswordResetRequested;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * @coversNothing
 */
class RequestPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_204_will_be_received_when_requesting_a_password_reset_request()
    {
        // Given
        Event::fake(PasswordResetRequested::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        // When
        $response = $this->post(route('account.password.request'), [
            'email' => $user->email,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_NO_CONTENT);

        Event::assertDispatched(PasswordResetRequested::class);
    }
}
