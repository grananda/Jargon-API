<?php

namespace App\Listeners;

use App\Events\Collaborator\CollaboratorAddedToProject;
use App\Mail\SendProjectCollaboratorEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendProjectCollaboratorInvitationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param \App\Events\Collaborator\CollaboratorAddedToProject $event
     *
     * @return void
     */
    public function handle(CollaboratorAddedToProject $event)
    {
        Mail::to($event->user)
            ->send(new SendProjectCollaboratorEmail($event->project, $event->user, $event->invitationToken))
        ;
    }
}
