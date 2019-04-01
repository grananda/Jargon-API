<?php


namespace Tests\Feature\Auth;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_non_existing_user_cannot_login()
    {
        // When
        $response = $this->post(route('auth.login'), [
            'email'    => $this->faker->email,
            'password' => $this->faker->password,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function an_inactive_user_cannot_login()
    {
        // Given
        $password = $this->faker->password;

        /** @var \App\Models\User $user */
        $user = $this->user('registered-user', [
            'password'     => bcrypt($password),
            'activated_at' => null,
        ]);

        // When
        $response = $this->post(route('auth.login'), [
            'email'    => $user->email,
            'password' => $password,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_user_can_login()
    {
        // Given
        Artisan::call('passport:install');

        $password = $this->faker->password;

        /** @var \App\Models\User $user */
        $user = $this->user('registered-user', [
            'password' => bcrypt($password),
        ]);

        // When
        $response = $this->post(route('auth.login'), [
            'email'    => $user->email,
            'password' => $password,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(auth()->user()->id, $user->id);
    }
}