<?php

namespace App\Mail;

use App\Models\Translations\Project;
use App\Models\User;
use Illuminate\Mail\Mailable;

class SendProjectCollaboratorEmail extends Mailable
{
    /**
     * Project instance.
     *
     * @var \App\Models\Translations\Project
     */
    protected $project;

    /**
     * User recipient.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Invitation token.
     *
     * @var string
     */
    protected $invitationToken;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Translations\Project $project
     * @param User                             $user
     * @param string                           $invitationToken
     */
    public function __construct(Project $project, User $user, string $invitationToken)
    {
        $this->project         = $project;
        $this->user            = $user;
        $this->invitationToken = $invitationToken;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = trans(' :project wants you to collaborate with them', ['project' => $this->project->title]);

        return $this->view('emails.project.collaboratorInvitation')
            ->subject($subject)
            ->with([
                'subject'         => $subject,
                'project'         => $this->project,
                'user'            => $this->user,
                'invitationToken' => $this->invitationToken,
            ])
        ;
    }
}
