<?php

namespace App\Mail;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Mail\Mailable;

class SendOrganizationCollaboratorEmail extends Mailable
{
    /**
     * Organization instance.
     *
     * @var \App\Models\Organization
     */
    protected $organization;

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
     * @param Organization $organization
     * @param User         $user
     * @param              $invitationToken
     */
    public function __construct(Organization $organization, User $user, string $invitationToken)
    {
        $this->organization    = $organization;
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
        return $this->view('emails.organization.collaboratorInvitation')
            ->subject($this->subject)
            ->with([
                'subject'         => trans($this->organization->name.' wants you to collaborate with them'),
                'organization'    => $this->organization,
                'user'            => $this->user,
                'invitationToken' => $this->invitationToken,
            ])
            ;
    }
}
