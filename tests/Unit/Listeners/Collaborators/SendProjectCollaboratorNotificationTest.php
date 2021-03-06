<?php

namespace Tests\Unit\Listeners\Collaborators;

use App\Events\Collaborator\CollaboratorAddedToProject;
use App\Listeners\SendProjectCollaboratorNotification;
use App\Listeners\SendTeamCollaboratorNotification;
use App\Mail\SendProjectCollaboratorEmail;
use App\Models\Translations\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * @group unit
 * @covers \App\Listeners\SendProjectCollaboratorNotification
 */
class SendProjectCollaboratorNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function email_is_sent_after_collaborator_is_added_to_a_project()
    {
        // Given
        Mail::fake(SendProjectCollaboratorEmail::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();

        $invitationToken = Str::random(Project::ITEM_TOKEN_LENGTH);

        /** @var \App\Events\Collaborator\CollaboratorAddedToProject $event */
        $event = new CollaboratorAddedToProject($project, $user, $invitationToken);

        /** @var SendTeamCollaboratorNotification $listener */
        $listener = new SendProjectCollaboratorNotification();

        // When
        $listener->handle($event);

        // Then
        Mail::assertSent(SendProjectCollaboratorEmail::class);
    }
}
