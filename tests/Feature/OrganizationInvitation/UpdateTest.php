<?php

namespace Tests\Feature\Api\OrganizationInvitation;


use App\Models\Organization;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Http\Response;


class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_404_will_be_returned_when_validating_an_invalid_invitation_token()
    {
        // Given
        $token = Str::random(Organization::ITEM_TOKEN_LENGTH);

        // When
        $response = $this->put(route('organizations.invitation.update', ['token' => $token]));

        // Then
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function a_403_will_be_returned_when_validating_an_expired_invitation_token()
    {
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->addMember($user, Organization::ORGANIZATION_DEFAULT_ROLE_ALIAS);

        $organization->collaborators()->updateExistingPivot($user->id, ['created_at'=> Carbon::now()->subDays(40)]);

        $token = $organization->collaborators()->where('user_id', $user->id)->first()->pivot->validation_token;

        // When
        $response = $this->put(route('organizations.invitation.update', ['token' => $token]));

        // Then
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function a_200_will_be_returned_when_validating_an_invitation_token()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->addMember($user, Organization::ORGANIZATION_DEFAULT_ROLE_ALIAS);

        $token = $organization->collaborators()->where('user_id', $user->id)->first()->pivot->validation_token;

        // When
        $response = $this->put(route('organizations.invitation.update', ['token' => $token]));

        // Then
        $response->assertStatus(Response::HTTP_OK);
    }
}
