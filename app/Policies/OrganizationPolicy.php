<?php

namespace App\Policies;

use App\Exceptions\SubscriptionLimitExceeded;
use App\Models\Organization;
use App\Models\User;
use Exception;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class OrganizationPolicy extends AbstractPolicy
{
    const ORGANIZATION_SUBSCRIPTION_CREATE   = 'api.client.subscription.organization.create.error';
    const ORGANIZATION_SUBSCRIPTION_ADD_USER = 'api.client.subscription.organization.addUser.error';

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
        return $organization->isMember($user);
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

        $userOrganizationCount = $user->organizations->filter(function ($org) use ($user) {
            /* @var $org \App\Models\Organization */
            return $org->isOwner($user) == true;
        })->count();

        if ($subscriptionOrganizationCount <= $userOrganizationCount && ! is_null($subscriptionOrganizationCount)) {
            throw new SubscriptionLimitExceeded(trans(self::ORGANIZATION_SUBSCRIPTION_CREATE), HttpResponse::HTTP_PAYMENT_REQUIRED);
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
     * @param User         $user
     * @param Organization $organization
     *
     * @throws Exception
     *
     * @return bool
     */
    public function addUser(User $user, Organization $organization)
    {
        $subscriptionCollaboratorCount = $user->subscription->subscriptionPlan->organization_collaborator_count;
        $userCollaboratorCount         = $organization->users->count();

        if ($subscriptionCollaboratorCount <= $userCollaboratorCount && ! is_null($subscriptionCollaboratorCount)) {
            throw new Exception(trans(self::ORGANIZATION_SUBSCRIPTION_ADD_USER), HttpResponse::HTTP_PAYMENT_REQUIRED);
        }

        return true;
    }

    /**
     * @param User         $user
     * @param Organization $organization
     *
     * @throws Exception
     *
     * @return bool
     */
    public function addTeam(User $user, Organization $organization)
    {
        $subscriptionOrganizationTeamCount = $user->subscription->subscriptionPlan->organization_team_count;
        $userOrganizationTeamCount         = $organization->teams->count();

        if ($subscriptionOrganizationTeamCount <= $userOrganizationTeamCount && ! is_null($subscriptionOrganizationTeamCount)) {
            throw new Exception(trans(self::ORGANIZATION_SUBSCRIPTION_ADD_USER), HttpResponse::HTTP_PAYMENT_REQUIRED);
        }

        return true;
    }

    /**
     * Determines is a user can store a project.
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
