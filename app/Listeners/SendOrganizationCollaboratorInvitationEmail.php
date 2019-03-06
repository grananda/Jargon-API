<?php

namespace App\Listeners;

use App\Events\CollaboratorAddedToOrganization;
use App\Mail\SendOrganizationCollaboratorEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendOrganizationCollaboratorInvitationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param CollaboratorAddedToOrganization $event
     *
     * @return void
     */
    public function handle(CollaboratorAddedToOrganization $event)
    {
        Mail::to($event->user)
            ->send(new SendOrganizationCollaboratorEmail($event->organization, $event->user, $event->invitationToken))
        ;
    }
}
