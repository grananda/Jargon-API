<?php

namespace App\Models\Traits;

use App\Models\Role;
use App\Models\User;

trait HasCollaborators
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function collaborators()
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
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function owners()
    {
        return $this->morphToMany(User::class, 'entity', 'collaborators')
            ->where('is_owner', true)
            ->withPivot([
                'is_valid',
                'role_id',
            ])
            ->withTimestamps()
        ;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isOwner(User $user)
    {
        foreach ($this->collaborators as $member) {
            if ($user->id === $member->id && $member->pivot->is_owner) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isMember(User $user)
    {
        $userMembers = array_column($this->collaborators()
            ->where('is_valid', true)
            ->get()
            ->toArray(), 'id');

        return array_search($user->id, $userMembers) > -1;
    }

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
     * Validate collaborator association to entity.
     *
     * @param \App\Models\User $user
     */
    public function validateMember(User $user)
    {
        $this->collaborators()->updateExistingPivot($user->id, ['is_valid' => true]);
    }

    /**
     * @param array $collaborators
     */
    public function setCollaborators(array $collaborators)
    {
        $_collaborators = [];
        $userMembersId  = array_column($this->collaborators->toArray(), 'id');

        foreach ($collaborators as $userId => $options) {
            $_collaborators[$userId]['role_id']  = $options['role_id'];
            $_collaborators[$userId]['is_owner'] = $options['is_owner'];
            $_collaborators[$userId]['is_valid'] = $options['is_valid'];

            if (array_search($userId, $userMembersId) === false) {
                $invitationToken                             = str_random(self::ITEM_TOKEN_LENGTH);
                $_collaborators[$userId]['validation_token'] = $invitationToken;

                /** @var \App\Models\User $user */
                $user = User::find($userId);

                if (! $options['is_owner']) {
                    $this->createAddCollaboratorEvent($this, $user, $invitationToken);
                }
            }
        }

        $this->collaborators()->syncWithoutDetaching($_collaborators);

        $this->load('collaborators');
    }
}
