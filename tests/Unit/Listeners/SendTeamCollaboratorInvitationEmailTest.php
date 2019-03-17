<?php

namespace Tests\Unit\Listeners;


use App\Events\CollaboratorAddedToTeam;
use App\Listeners\SendTeamCollaboratorInvitationEmail;
use App\Mail\SendTeamCollaboratorEmail;
use App\Models\Organization;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class SendTeamCollaboratorInvitationEmailTest extends TestCase
{
    /** @test */
    public function email_is_sent_after_collaborator_is_added_to_organization()
    {
        // Given
        Mail::fake();

        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();

        $invitationToken = Str::random(Organization::ITEM_TOKEN_LENGTH);

        /** @var \App\Events\CollaboratorAddedToTeam $event */
        $event = new CollaboratorAddedToTeam($team, $user, $invitationToken);

        /** @var SendTeamCollaboratorInvitationEmail $listener */
        $listener = new SendTeamCollaboratorInvitationEmail();

        // When
        $listener->handle($event);

        // Then
        Mail::assertSent(SendTeamCollaboratorEmail::class);
    }
}
