<?php

namespace App\Models\Translations;

use App\Events\Collaborator\CollaboratorAddedToProject;
use App\Models\BaseEntity;
use App\Models\Dialect;
use App\Models\Organization;
use App\Models\Team;
use App\Models\Traits\HasCollaborators;
use App\Models\Traits\HasUuid;
use App\Models\User;

/**
 * @property \App\Models\Translations\GitConfig    gitConfig
 * @property \Illuminate\Support\Collection        rootNodes
 * @property \Illuminate\Support\Collection        dialects
 * @property \App\Models\Translations\JargonOption jargonOptions
 * @property string                                 uuid
 */
class Project extends BaseEntity
{
    use HasUuid;
    use HasCollaborators;

    const ITEM_TOKEN_LENGTH = 50;

    const PROJECT_OWNER_ROLE_ALIAS      = 'project-admin';
    const PROJECT_MANAGER_ROLE_ALIAS    = 'project-manager';
    const PROJECT_TRANSLATOR_ROLE_ALIAS = 'project-translator';

    const PROJECT_DEFAULT_ROLE_ALIAS = 'project-manager';

    protected $fillable = [
        'title',
        'description',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('team_id', 'project_id')
            ->withTimestamps()
        ;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dialects()
    {
        return $this->belongsToMany(Dialect::class)
            ->withPivot('dialect_id', 'is_default')
            ->withTimestamps()
            ->orderBy('locale')
        ;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rootNodes()
    {
        return $this->hasMany(Node::class, 'project_id')
            ->whereNull('parent_id')
            ->orderBy('sort_index')
        ;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function nodes()
    {
        return $this->hasMany(Node::class)
            ->orderBy('sort_index')
        ;
    }

    /**
     * Git project options.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gitConfig()
    {
        return $this->hasOne(GitConfig::class);
    }

    /**
     * Git project file hashes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gitFiles()
    {
        return $this->hasOne(GitFileHash::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function jargonOptions()
    {
        return $this->hasOne(JargonOption::class);
    }

    /**
     * @return mixed
     */
    public function defaultDialect()
    {
        return $this->dialects()
            ->where('is_default', true)
            ->first()
        ;
    }

    /**
     * @param Organization $organization
     *
     * @return Project
     */
    public function setOrganization(Organization $organization = null)
    {
        $this->organization()->associate($organization);
        $this->save();

        return $this->fresh();
    }

    /**
     * @param array $dialects
     *
     * @return Project
     *
     * @internal param array $languages
     */
    public function setDialects(array $dialects)
    {
        $this->dialects()->sync($dialects);
        $this->load('dialects');

        return $this;
    }

    /**
     * @param \App\Models\User $user
     */
    public function setOwner(User $user)
    {
        $this->addOwner($user, self::PROJECT_OWNER_ROLE_ALIAS);
    }

    /**
     * @param \App\Models\User $user
     * @param string|null      $role
     */
    public function setMember(User $user, string $role = null)
    {
        $this->addMember($user, $role ?? self::PROJECT_DEFAULT_ROLE_ALIAS);
    }

    /**
     * @param array $teams
     *
     * @return Project
     */
    public function setTeams(array $teams)
    {
        $this->teams()->sync($teams);

        return $this->refresh();
    }

    /**
     * @param self             $project
     * @param \App\Models\User $user
     * @param string           $invitationToken
     */
    public function createAddCollaboratorEvent(self $project, User $user, string $invitationToken)
    {
        event(new CollaboratorAddedToProject($this, $user, $invitationToken));
    }

    /**
     * @return bool
     */
    public function hasGitHubAccess()
    {
        return $this->gitHubConfig && $this->gitHubConfig->access_token;
    }
}
