<?php

namespace Tests\Feature\Organization;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->get(route('organizations.index'));

        // Then
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_200_will_be_returned_when_listing_all_organizations_for_a_non_valid_member()
    {
        // Given
        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($owner);

        // When
        $response = $this->signIn($user)->get(route('organizations.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    /** @test */
    public function a_200_will_be_returned_when_listing_all_organizations_for_an_owner()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($user);

        // When
        $response = $this->signIn($user)->get(route('organizations.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $organization->uuid]);
    }

    /** @test */
    public function a_200_will_be_returned_when_listing_all_organizations_for_a_team_member()
    {
        /** TODO Complete list org-team test */
        $this->assertTrue(true);
    }

    /** @test */
    public function a_200_will_be_returned_when_listing_all_organizations_for_a_project_member()
    {
        /** TODO Complete list org-project test */
        $this->assertTrue(true);
    }
}
