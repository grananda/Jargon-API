<?php

namespace App\Models\Traits;

use App\Models\Role;
use App\Models\User;
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
     * Returns entity users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function users()
    {
        return $this->morphToMany(User::class, 'entity', 'collaborators')
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
     * Returns entity active members.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function nonActiveMembers()
    {
        return $this->morphToMany(User::class, 'entity', 'collaborators')
            ->where('is_owner', false)
            ->where('is_valid', false)
            ->withPivot([
                'is_valid',
                'role_id',
                'validation_token',
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
        $this->nonActiveMembers()->updateExistingPivot($user->id, ['is_valid' => true]);
    }

    /**
     * Adds a standard collaborator member to an entity.
     *
     * @param \App\Models\User $user
     * @param string           $role
     */
    protected function addMember(User $user, string $role)
    {
        $invitationToken = Str::random(self::ITEM_TOKEN_LENGTH);

        $this->users()->syncWithoutDetaching([
                $user->id => [
                    'is_valid'         => false,
                    'validation_token' => $invitationToken,
                    'is_owner'         => false,
                    'role_id'          => Role::where('alias', $role)->first()->id,
                ],
            ]
        );

        $this->load('users');

        $this->createAddCollaboratorEvent($this, $user->fresh(), $invitationToken);
    }

    /**
     * Adds a new collaborator as an entity owner.
     *
     * @param \App\Models\User $user
     * @param string           $role
     */
    protected function addOwner(User $user, string $role)
    {
        $this->users()->syncWithoutDetaching([
                $user->id => [
                    'is_valid' => true,
                    'is_owner' => true,
                    'role_id'  => Role::where('alias', $role)->first()->id,
                ],
            ]
        );

        $this->load('users');
    }

    /**
     * Adds a set of collaborator to an entity.
     *
     * @param array $collaborators
     */
    public function setCollaborators(array $collaborators)
    {
        /** @var \Illuminate\Support\Collection $members */
        $members = collect($collaborators)->mapWithKeys(function ($item) {
            /** @var \App\Models\User $user */
            $user = $this->users()->where('uuid', $item['id'])->first() ?? User::findByUuidOrFail($item['id']);

            /** @var \App\Models\Role $role */
            $role = Role::where('alias', $item['role'])->first()->id;

            /** @var string $invitationToken */
            $invitationToken = $user->pivot->validation_token ?? Str::random(self::ITEM_TOKEN_LENGTH);

            /** @var bool $isValid */
            $isValid = $user->pivot->is_valid ?? false;

            $collaborator = [
                $user->id => [
                    'is_valid'         => $item['owner'] ? $item['owner'] : $isValid,
                    'is_owner'         => $item['owner'],
                    'role_id'          => $role,
                    'validation_token' => $invitationToken,
                ],
            ];

            if (! $item['owner'] && ! isset($user->pivot->validation_token)) {
                $this->createAddCollaboratorEvent($this, $user, $invitationToken);
            }

            return $collaborator;
        });

        $this->detachCollaborators();
        $this->users()->syncWithoutDetaching($members);

        $this->load('users');
    }

    /**
     * Detach all non owner collaborators from entity.
     */
    private function detachCollaborators()
    {
        foreach ($this->users as $collaborator) {
            if (! $collaborator->pivot->is_owner) {
                $this->users()->detach($collaborator->pivot->user_id);
            }
        }
    }
}
