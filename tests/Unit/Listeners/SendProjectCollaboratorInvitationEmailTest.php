<?php

namespace Tests\Unit\Listeners;


use App\Events\CollaboratorAddedToProject;
use App\Listeners\SendProjectCollaboratorInvitationEmail;
use App\Listeners\SendTeamCollaboratorInvitationEmail;
use App\Mail\SendProjectCollaboratorEmail;
use App\Models\Translations\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class SendProjectCollaboratorInvitationEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function email_is_sent_after_collaborator_is_added_to_a_project()
    {
        // Given
        Mail::fake();

        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();

        $invitationToken = Str::random(Project::ITEM_TOKEN_LENGTH);

        /** @var \App\Events\CollaboratorAddedToTeam $event */
        $event = new CollaboratorAddedToProject($project, $user, $invitationToken);

        /** @var SendTeamCollaboratorInvitationEmail $listener */
        $listener = new SendProjectCollaboratorInvitationEmail();

        // When
        $listener->handle($event);

        // Then
        Mail::assertSent(SendProjectCollaboratorEmail::class);
    }
}
