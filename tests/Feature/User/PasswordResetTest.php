<?php


namespace Tests\Feature\User;


use App\Events\User\PasswordResetRequested;
use App\Repositories\PasswordResetRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_reset_password_with_token()
    {
        // Given
        Artisan::call('passport:install');
        Event::fake(PasswordResetRequested::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var PasswordResetRepository $passwordResetRepository */
        $passwordResetRepository = resolve(PasswordResetRepository::class);

        $token = $passwordResetRepository->createPasswordReset($user);

        $password = $this->faker->password(10);

        // When
        $response = $this->post(route('account.password.reset'),
            [
                'token'                 => $token,
                'email'                 => $user->email,
                'password'              => $password,
                'password_confirmation' => $password,
            ]
        );

        // Then
        $response->assertStatus(Response::HTTP_OK);

        Event::assertDispatched(PasswordResetRequested::class);
    }
}