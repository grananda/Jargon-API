<?php

namespace App\Events\Collaborator;

use App\Models\Translations\Project;
use App\Models\User;

class CollaboratorAddedToProject
{
    /**
     * The Team instance.
     *
     * @var \App\Models\Translations\Project
     */
    public $project;

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
     * @param \App\Models\Translations\Project $project
     * @param \App\Models\User                 $user
     * @param string                           $invitationToken
     */
    public function __construct(Project $project, User $user, string $invitationToken)
    {
        $this->project         = $project;
        $this->user            = $user;
        $this->invitationToken = $invitationToken;
    }
}
