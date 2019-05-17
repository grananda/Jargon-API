<?php

namespace App\Repositories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Connection;

class OrganizationRepository extends CoreRepository
{
    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection $dbConnection
     * @param \App\Models\Organization        $model
     */
    public function __construct(Connection $dbConnection, Organization $model)
    {
        parent::__construct($dbConnection, $model);
    }

    /**
     * Gets all items where user is member.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllByMember(User $user)
    {
        return $this->getQuery()
            ->whereHas('collaborators', function ($query) use ($user) {
                /* @var \Illuminate\Database\Query\Builder $query */
                $query->where('collaborators.user_id', $user->id);
                $query->where('collaborators.is_valid', true);
            })
            ->orWhereHas('projects.teams.collaborators', function ($query) use ($user) {
                /* @var \Illuminate\Database\Query\Builder $query */
                $query->where('collaborators.user_id', $user->id);
                $query->where('collaborators.is_valid', true);
            })
            ->orwhereHas('projects.collaborators', function ($query) use ($user) {
                /* @var \Illuminate\Database\Query\Builder $query */
                $query->where('collaborators.user_id', $user->id);
                $query->where('collaborators.is_valid', true);
            })
            ->orderByDesc('id')
            ->get()
            ;
    }

    /**
     * Creates a new organization for a given user as owner.
     *
     * @param \App\Models\User $user
     * @param array            $attributes
     *
     * @throws \Throwable
     *
     * @return \App\Models\Organization
     */
    public function createOrganization(User $user, array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($user, $attributes) {
            /** @var \App\Models\Organization $organization */
            $organization = $this->createWithOwner($user, $attributes);

            return $organization->fresh();
        });
    }

    /**
     * Updates an existing organization.
     *
     * @param \App\Models\Organization $organization
     * @param array                    $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function updateOrganization(Organization $organization, array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($organization, $attributes) {
            /** @var \App\Models\Organization $organization */
            $organization = $this->update($organization, $attributes);

            return $organization->fresh();
        });
    }
}
