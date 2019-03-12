<?php

namespace App\Models\Traits;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasCollaborators
{
    /**
     * Returns entity active collaborators.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function collaborators()
    {
        return $this->morphToMany(User::class, 'entity', 'collaborators')
            ->where('is_valid', true)
            ->withPivot([
                'is_owner',
                'is_valid',
                'role_id',
                'validation_token',
            ])
            ->withTimestamps()
        ;
    }

    /**
     * Returns entity active members.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function members()
    {
        return $this->morphToMany(User::class, 'entity', 'collaborators')
            ->where('is_owner', false)
            ->where('is_valid', true)
            ->withPivot([
                'is_valid',
                'role_id',
            ])
            ->withTimestamps()
        ;
    }

    /**
     * Returns entity active owners.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function owners()
    {
        return $this->morphToMany(User::class, 'entity', 'collaborators')
            ->where('is_owner', true)
            ->where('is_valid', true)
            ->withPivot([
                'is_valid',
                'role_id',
            ])
            ->withTimestamps()
        ;
    }

    /**
     * Checks if a collaborator is and entity owner.
     *
     * @param User $user
     *
     * @return bool
     */
    public function isOwner(User $user)
    {
        return (bool) $this->owners()->where('user_id', $user->id)->first();
    }

    /**
     * Checks is a collaborator is an active entity member.
     *
     * @param User $user
     *
     * @return bool
     */
    public function isMember(User $user)
    {
        return (bool) $this->members()->where('user_id', $user->id)->first();
    }

    /**
     * Validate collaborator association to entity.
     *
     * @param \App\Models\User $user
     */
    public function validateMember(User $user)
    {
        $this->collaborators()->updateExistingPivot($user->id, ['is_valid' => true]);
    }

    /**
     * Adds a standard collaborator member to an entity.
     *
     * @param \App\Models\User $user
     * @param string           $role
     */
    public function addMember(User $user, string $role)
    {
        $this->setCollaborators([
                $user->id => [
                    'is_valid' => false,
                    'is_owner' => false,
                    'role_id'  => Role::where('alias', $role)->first()->id,
                ],
            ]
        );
    }

    /**
     * Adds a new collaborator as an entity owner.
     *
     * @param \App\Models\User $user
     * @param string           $role
     */
    protected function addOwner(User $user, string $role)
    {
        $this->setCollaborators([
                $user->id => [
                    'is_valid' => true,
                    'is_owner' => true,
                    'role_id'  => Role::where('alias', $role)->first()->id,
                ],
            ]
        );
    }

    /**
     * Adds a set of collaborator to an entity.
     *
     * @param array $collaborators
     */
    private function setCollaborators(array $collaborators)
    {
        /** @var array $currentCollaborators */
        $currentCollaborators = array_column($this->collaborators()->get()->toArray(), 'id');

        foreach ($collaborators as $userId => $options) {
            if (array_search($userId, $currentCollaborators) === false) {
                $invitationToken = Str::random(self::ITEM_TOKEN_LENGTH);

                Arr::set($collaborators, $userId.'.validation_token', $invitationToken);
                Arr::set($collaborators, $userId.'.is_valid', $options['is_owner']);

                /** @var \App\Models\User $user */
                $user = User::find($userId);

                if (! $options['is_owner']) {
                    $this->createAddCollaboratorEvent($this, $user, $invitationToken);
                }
            }
        }

        $this->detachCollaborators();
        $this->collaborators()->syncWithoutDetaching($collaborators);

        $this->load('collaborators');
    }

    /**
     * Detach all non owner collaborators from entity.
     */
    private function detachCollaborators()
    {
        foreach ($this->collaborators as $collaborator) {
            if (! $collaborator->pivot->is_owner) {
                $this->collaborators()->detach($collaborator->pivot->user_id);
            }
        }
    }
}
