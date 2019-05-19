<?php

namespace Tests\Feature\Api\Organization;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

/**
 * @group feature
 * @coversNothing
 */
class StoreTest extends TestCase
{
    use RefreshDatabase,
        CreateActiveSubscription;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->post(route('organizations.store'));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_creates_a_new_organization_without_organization_quota()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        $this->createActiveSubscription(
            $user,
            'professional-month-eur',
            ['organization_count' => 0]);

        $data = [
            'name'        => $this->faker->word,
            'description' => $this->faker->text,
        ];

        // When
        $response = $this->signIn($user)->post(route('organizations.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_if_the_user_creates_a_new_organization()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        $this->createActiveSubscription($user, 'professional-month-eur');

        $data = [
            'name'        => $this->faker->word,
            'description' => $this->faker->text,
        ];

        // When
        $response = $this->signIn($user)->post(route('organizations.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['name' => $data['name']]);
        $this->assertDatabaseHas('organizations', [
            'uuid' => $response->json('data')['id'],
        ]);
        $this->assertDatabaseHas('collaborators', [
            'entity_type' => 'organization',
            'is_owner'    => 1,
            'user_id'     => $user->id,
        ]);
    }
}
