<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use App\Repositories\OrganizationRepository;
use Exception;

class OrganizationPolicy extends AbstractPolicy
{
    /**
     * @var \App\Repositories\OrganizationRepository
     */
    private $organizationRepository;

    /**
     * OrganizationPolicy constructor.
     *
     * @param \App\Repositories\OrganizationRepository $organizationRepository
     */
    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * Determines is a user can list organizations.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function list(User $user)
    {
        return true;
    }

    /**
     * @param User         $user
     * @param Organization $organization
     *
     * @return bool
     */
    public function show(User $user, Organization $organization)
    {
        return (bool) $this->organizationRepository->findAllByMember($user)->map(function ($item) use ($organization) {
            return $item->id === $organization->id;
        })->count();
    }

    /**
     * @param User $user
     *
     * @throws Exception
     *
     * @return bool
     */
    public function create(User $user)
    {
        $subscriptionOrganizationCount = $user->activeSubscription->options()->where('option_key', 'organization_count')->first()->option_value;

        $currentOrganizationCount = $user->organizations->filter(function ($org) use ($user) {
            /* @var $org \App\Models\Organization */
            return $org->isOwner($user) == true;
        })->count();

        if ($subscriptionOrganizationCount <= $currentOrganizationCount && ! is_null($subscriptionOrganizationCount)) {
            return false;
        }

        return true;
    }

    /**
     * @param User         $user
     * @param Organization $organization
     *
     * @return bool
     */
    public function update(User $user, Organization $organization)
    {
        return $organization->isOwner($user);
    }

    /**
     * @param User         $user
     * @param Organization $organization
     *
     * @return bool
     */
    public function delete(User $user, Organization $organization)
    {
        return $organization->isOwner($user);
    }

    /**
     * Determines if a user can store a project.
     *
     * @param \App\Models\User         $user
     * @param \App\Models\Organization $organization
     *
     * @return bool
     */
    public function addDependency(User $user, Organization $organization)
    {
        return $organization->isOwner($user);
    }
}
