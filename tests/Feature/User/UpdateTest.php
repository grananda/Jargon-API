<?php

namespace Tests\Feature\User;

use App\Jobs\UpdateStripeCustomer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * @coversNothing
 */
class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_200_will_be_returned_when_a_user_is_updated()
    {
        // Given
        Bus::fake(UpdateStripeCustomer::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        $data = [
            'name'  => $this->faker->name,
            'email' => $this->faker->email,
        ];

        // When
        $response = $this->signIn($user)->put(route('users.update', [$user->uuid]), $data);

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('users', [
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        Bus::assertDispatched(UpdateStripeCustomer::class);
    }
}
