<?php

namespace Tests\Feature\Api\Organization;

use App\Events\CollaboratorAddedToOrganization;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

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
        $user = factory(User::class)->create();

        /** @var \App\Models\User $collaborator */
        $collaborator = factory(User::class)->create();

        $this->createActiveSubscription(
            $user,
            'professional',
            ['organization_count' => 0]);

        $data = [
            'name'          => $this->faker->word,
            'description'   => $this->faker->text,
            'teams'         => [],
            'collaborators' => [
                [$collaborator->uuid, Organization::ORGANIZATION_DEFAULT_ROLE_ALIAS],
            ],
        ];

        // When
        $response = $this->signIn($user)->post(route('organizations.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_creates_a_new_organization_without_collaborator_quota()
    {
        // Given
        Event::fake([CollaboratorAddedToOrganization::class]);

        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\User $collaborator */
        $collaborator = factory(User::class)->create();

        $this->createActiveSubscription(
            $user,
            'professional',
            ['collaborator_count' => 0]);

        $data = [
            'name'          => $this->faker->word,
            'description'   => $this->faker->text,
            'teams'         => [],
            'collaborators' => [
                [$collaborator->uuid, Organization::ORGANIZATION_DEFAULT_ROLE_ALIAS],
            ],
        ];

        // When
        $response = $this->signIn($user)->post(route('organizations.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        Event::assertNotDispatched(CollaboratorAddedToOrganization::class);
    }

    /** @test */
    public function a_200_will_be_returned_if_the_user_creates_a_new_organization()
    {
        // Given
        Event::fake([CollaboratorAddedToOrganization::class]);

        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\User $collaborator */
        $collaborator = factory(User::class)->create();

        $this->createActiveSubscription($user, 'professional');

        $data = [
            'name'          => $this->faker->word,
            'description'   => $this->faker->text,
            'teams'         => [],
            'collaborators' => [
                [$collaborator->uuid, Organization::ORGANIZATION_DEFAULT_ROLE_ALIAS],
            ],
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
        $this->assertDatabaseHas('collaborators', [
            'entity_type' => 'organization',
            'is_owner'    => 0,
            'user_id'     => $collaborator->id,
        ]);

        Event::assertDispatched(CollaboratorAddedToOrganization::class);
    }
}
