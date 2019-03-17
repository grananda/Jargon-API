<?php

namespace App\Repositories;

use App\Models\Organization;
use App\Models\User;
use App\Repositories\Traits\InvitationTrait;
use Illuminate\Database\Connection;

class OrganizationRepository extends CoreRepository
{
    use InvitationTrait;

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
