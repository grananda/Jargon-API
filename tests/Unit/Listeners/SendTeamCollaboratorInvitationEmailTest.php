<?php

namespace Tests\Unit\Listeners;


use App\Events\Collaborator\CollaboratorAddedToTeam;
use App\Listeners\SendTeamCollaboratorInvitationEmail;
use App\Mail\SendTeamCollaboratorEmail;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class SendTeamCollaboratorInvitationEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function email_is_sent_after_collaborator_is_added_to_a_team()
    {
        // Given
        Mail::fake();

        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();

        $invitationToken = Str::random(Team::ITEM_TOKEN_LENGTH);

        /** @var \App\Events\Collaborator\CollaboratorAddedToTeam $event */
        $event = new CollaboratorAddedToTeam($team, $user, $invitationToken);

        /** @var SendTeamCollaboratorInvitationEmail $listener */
        $listener = new SendTeamCollaboratorInvitationEmail();

        // When
        $listener->handle($event);

        // Then
        Mail::assertSent(SendTeamCollaboratorEmail::class);
    }
}
