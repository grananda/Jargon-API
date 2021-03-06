<?php

namespace App\Models;

use App\Events\Collaborator\CollaboratorAddedToTeam;
use App\Models\Traits\HasCollaborators;
use App\Models\Traits\HasUuid;
use App\Models\Translations\Project;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends BaseEntity
{
    use HasCollaborators;
    use
        HasUuid;

    const ITEM_TOKEN_LENGTH = 50;

    const TEAM_OWNER_ROLE_ALIAS      = 'project-admin';
    const TEAM_MANAGER_ROLE_ALIAS    = 'project-manager';
    const TEAM_TRANSLATOR_ROLE_ALIAS = 'project-translator';
    const TEAM_DEFAULT_ROLE_ALIAS    = 'project-user';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * @return BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class)
            ->withTimestamps()
        ;
    }

    /**
     * @param \App\Models\Team $team
     * @param \App\Models\User $user
     * @param string           $invitationToken
     */
    public function createAddCollaboratorEvent(self $team, User $user, string $invitationToken)
    {
        event(new CollaboratorAddedToTeam($team, $user, $invitationToken));
    }

    /**
     * @param \App\Models\User $user
     */
    public function setOwner(User $user)
    {
        $this->addOwner($user, self::TEAM_OWNER_ROLE_ALIAS);
    }

    public function setMember(User $user, string $role = null)
    {
        $this->addMember($user, $role ?? self::TEAM_DEFAULT_ROLE_ALIAS);
    }
}
