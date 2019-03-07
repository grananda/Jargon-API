<?php

namespace App\Events;

use App\Models\Team;
use App\Models\User;

class CollaboratorAddedToTeam
{
    /**
     * The Team instance.
     *
     * @var \App\Models\Team
     */
    public $team;

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
     * @param \App\Models\Team $team
     * @param \App\Models\User $user
     * @param string           $invitationToken
     */
    public function __construct(Team $team, User $user, string $invitationToken)
    {
        $this->team            = $team;
        $this->user            = $user;
        $this->invitationToken = $invitationToken;
    }
}
