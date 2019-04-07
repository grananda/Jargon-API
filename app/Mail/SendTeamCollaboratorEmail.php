<?php

namespace App\Mail;

use App\Models\Team;
use App\Models\User;
use Illuminate\Mail\Mailable;

class SendTeamCollaboratorEmail extends Mailable
{
    /**
     * Team instance.
     *
     * @var \App\Models\Team
     */
    protected $team;

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
     * @param \App\Models\Team $team
     * @param User             $user
     * @param string           $invitationToken
     */
    public function __construct(Team $team, User $user, string $invitationToken)
    {
        $this->team            = $team;
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
        $subject = trans(':team wants you to collaborate with them', ['team' => $this->team->name]);

        return $this->view('emails.team.collaboratorInvitation')
            ->subject($this->subject)
            ->with([
                'subject'         => $subject,
                'team'            => $this->team,
                'user'            => $this->user,
                'invitationToken' => $this->invitationToken,
            ])
        ;
    }
}
