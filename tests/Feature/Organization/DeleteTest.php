<?php

namespace Tests\Feature\Organization;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group feature
 * @covers \App\Http\Controllers\Organization\OrganizationApiController::destroy
 */
class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($user);

        // When
        $response = $this->delete(route('organizations.destroy', [$organization->uuid]));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_has_non_owner_organization_access()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($owner);

        // When
        $response = $this->signIn($user)->delete(route('organizations.destroy', [$organization->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_deleting_an_organization_by_owner()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($user);

        // When
        $response = $this->signIn($user)->delete(route('organizations.destroy', [$organization->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('organizations', [
            'uuid' => $organization->uuid,
        ]);
    }
}
