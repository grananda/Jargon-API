<?php

namespace App\Listeners;

use App\Events\CollaboratorAddedToTeam;
use App\Mail\SendTeamCollaboratorEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendTeamCollaboratorInvitationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param \App\Events\CollaboratorAddedToTeam $event
     *
     * @return void
     */
    public function handle(CollaboratorAddedToTeam $event)
    {
        Mail::to($event->user)
            ->send(new SendTeamCollaboratorEmail($event->team, $event->user, $event->invitationToken))
        ;
    }
}
