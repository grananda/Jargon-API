<?php

namespace Tests\Feature\TeamInvitation;


use App\Models\Organization;
use App\Models\Team;
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
        $response = $this->put(route('teams.invitation.update', ['token' => $token]));

        // Then
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function a_403_will_be_returned_when_validating_an_expired_invitation_token()
    {
        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();
        $this->signIn($owner);

        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);
        $team->setMember($user, Team::TEAM_DEFAULT_ROLE_ALIAS);

        $team->nonActiveMembers()->updateExistingPivot($user->id, ['created_at'=> Carbon::now()->subDays(40)]);

        $token = $team->nonActiveMembers()->where('user_id', $user->id)->first()->pivot->validation_token;

        // When
        $response = $this->put(route('teams.invitation.update', ['token' => $token]));

        // Then
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function a_200_will_be_returned_when_validating_an_invitation_token()
    {
        // Given
        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();
        $this->signIn($owner);

        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);
        $team->setMember($user, Team::TEAM_DEFAULT_ROLE_ALIAS);

        $token = $team->nonActiveMembers()->where('user_id', $user->id)->first()->pivot->validation_token;

        // When
        $response = $this->put(route('teams.invitation.update', ['token' => $token]));

        // Then
        $response->assertStatus(Response::HTTP_OK);
    }
}
