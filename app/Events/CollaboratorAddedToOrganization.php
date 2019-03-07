<?php

namespace App\Events;

use App\Models\Organization;
use App\Models\User;

class CollaboratorAddedToOrganization
{
    /**
     * The Organization instance.
     *
     * @var \App\Models\Organization
     */
    public $organization;

    /**
     * The User recipient instance.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * The user unique invitation token.
     *
     * @var string
     */
    public $invitationToken;

    /**
     * CollaboratorAddedToOrganization constructor.
     *
     * @param \App\Models\Organization $organization
     * @param \App\Models\User         $user
     * @param string                   $invitationToken
     */
    public function __construct(Organization $organization, User $user, string $invitationToken)
    {
        $this->organization    = $organization;
        $this->user            = $user;
        $this->invitationToken = $invitationToken;
    }
}
