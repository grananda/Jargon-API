<?php

namespace Tests\Feature\Api\Organization;


use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public function setUp():void
    {
        parent::setUp();
    }

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($user);

        // When
        $response = $this->get(route('api.organizations.show', [$organization->uuid]));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_has_no_organization_access()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($owner);

        // When
        $response = $this->signIn($user)->get(route('api.organizations.show', [$organization->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_when_showing_an_organization_to_a_non_valid_member()
    {
        // Given
        $user = factory(User::class)->create();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($owner);
        $organization->addMember($user, Organization::ORGANIZATION_DEFAULT_ROLE_ALIAS);

        // When
        $response = $this->signIn($user)->get(route('api.organizations.show', [$organization->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_showing_an_organization_to_owner()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($user);

        // When
        $response = $this->signIn($user)->get(route('api.organizations.show', [$organization->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $organization->uuid,
        ]);
    }

    /** @test */
    public function a_200_will_be_returned_when_showing_an_organization_to_member()
    {
        // Given
        $user = factory(User::class)->create();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($owner);
        $organization->addMember($user, Organization::ORGANIZATION_DEFAULT_ROLE_ALIAS);
        $organization->validateMember($user);

        // When
        $response = $this->signIn($user)->get(route('api.organizations.show', [$organization->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $organization->uuid,
        ]);
    }
}
