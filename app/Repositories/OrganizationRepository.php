<?php

namespace App\Repositories;

use App\Models\Organization;
use App\Models\User;
use Carbon\Carbon;
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

            $this->addCollaborators($organization, $attributes['collaborators']);

            $organization->teams()->sync($attributes['teams']);

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

            $this->addCollaborators($organization, $attributes['collaborators']);

            $organization->teams()->sync($attributes['teams']);

            return $organization->fresh();
        });
    }

    /**
     * Returns organization realated to user validation token.
     *
     * @param string $token
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function findOneByInvitationToken(string $token)
    {
        return $this->getModel()
            ->with('collaborators')
            ->whereHas('nonActiveMembers', function ($query) use ($token) {
                $expDate = Carbon::now()->subDays(Organization::INVITATION_EXPIRATION_DAYS);

                $query->where('validation_token', $token);
                $query->whereDate('collaborators.created_at', '>=', $expDate);
            })->firstOrFail();
    }

    /**
     * Validate user invitation.
     *
     * @param \App\Models\Organization $organization
     */
    public function validateInvitation(Organization $organization)
    {
        $organization->validateMember($organization->collaborators()->first());
    }
}
