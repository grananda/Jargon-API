<?php

namespace Tests\Feature\User;

use App\Events\User\UserActivationTokenGenerated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * @group feature
 * @coversNothing
 */
class StoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_200_will_be_returned_when_a_user_is_created()
    {
        // Given
        Event::fake(UserActivationTokenGenerated::class);

        $password = $this->faker->password;
        $data     = [
            'name'                  => $this->faker->name,
            'email'                 => $this->faker->email,
            'password'              => $password,
            'password_confirmation' => $password,
        ];

        // When
        $response = $this->post(route('users.store'), $data);

        // Then
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('users', [
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        Event::assertDispatched(UserActivationTokenGenerated::class);
    }
}
