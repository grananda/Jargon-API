<?php

namespace Tests\Feature\Api\Organization;


use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\Response;
use Tests\traits\CreateActiveSubscription;

class UpdateTest extends TestCase
{
    use RefreshDatabase,
        CreateActiveSubscription;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->put(route('organizations.update', [1]));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_a_non_owner_updates_an_organization()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();
        $this->signIn($owner);

        $this->createActiveSubscription($owner, 'professional');

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($owner);

        $data = [
            'name' => $this->faker->sentence,
        ];

        // When
        $response = $this->signIn($user)->put(route('organizations.update', [$organization->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_if_an_owner_updates_an_organization()
    {
        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($owner);

        $this->createActiveSubscription(
            $owner,
            'professional'
        );

        $data = [
            'name' => $this->faker->sentence,
        ];

        // When
        $response = $this->signIn($owner)->put(route('organizations.update', [$organization->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['name' => $data['name']]);
        $this->assertDatabaseHas('organizations', [
            'uuid'  => $response->json('data')['id'],
            'name' => $response->json('data')['name'],
        ]);
        $this->assertDatabaseHas('collaborators', [
            'entity_type' => 'organization',
            'is_owner'    => 1,
            'user_id'     => $owner->id,
        ]);
    }
}
