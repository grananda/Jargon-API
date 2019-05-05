<?php

namespace Tests\Feature\ProjectInvitation;

use App\Models\Organization;
use App\Models\Team;
use App\Models\Translations\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * @coversNothing
 */
class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_404_will_be_returned_when_validating_an_invalid_invitation_token()
    {
        // Given
        $token = Str::random(Organization::ITEM_TOKEN_LENGTH);

        // When
        $response = $this->put(route('projects.invitation.update', ['token' => $token]));

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
        $user = $this->user();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($owner);
        $project->setMember($user, Project::PROJECT_DEFAULT_ROLE_ALIAS);

        $project->nonActiveMembers()->updateExistingPivot($user->id, ['created_at' => Carbon::now()->subDays(40)]);

        $token = $project->nonActiveMembers()->where('user_id', $user->id)->first()->pivot->validation_token;

        // When
        $response = $this->put(route('projects.invitation.update', ['token' => $token]));

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
        $user = $this->user();

        /** @var \App\Models\User $validMember */
        $validMember = $this->user();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($owner);

        $project->setMember($validMember, Team::TEAM_DEFAULT_ROLE_ALIAS);
        $project->validateMember($validMember);

        $project->setMember($user, Team::TEAM_DEFAULT_ROLE_ALIAS);

        $token = $project->nonActiveMembers()->where('user_id', $user->id)->first()->pivot->validation_token;

        // When
        $response = $this->put(route('projects.invitation.update', ['token' => $token]));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $this->assertSame($project->fresh()->collaborators()->count(), 3);
    }
}
