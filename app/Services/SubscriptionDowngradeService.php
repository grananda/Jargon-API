<?php

namespace App\Services;

use App\Exceptions\SubscriptionDowngradeRequirementException;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\User;

class SubscriptionDowngradeService
{
    /**
     * Checks for downgrade requirements.
     *
     * @param \App\Models\User                           $user
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan
     *
     * @throws \Throwable
     *
     * @return bool
     */
    public function checkSubscriptionPlanDowngradeRules(User $user, SubscriptionPlan $subscriptionPlan)
    {
        throw_if($subscriptionPlan->getAllowedOrganizations() < $user->getActiveOrganizations(),
            new SubscriptionDowngradeRequirementException(trans('Current organization quota cannot be adjusted to new subscription plan')));

        throw_if($subscriptionPlan->getAllowedTeams() < $user->getActiveTeams(),
            new SubscriptionDowngradeRequirementException(trans('Current team quota cannot be adjusted to new subscription plan')));

        throw_if($subscriptionPlan->getAllowedProjects() < $user->getActiveProjects(),
            new SubscriptionDowngradeRequirementException(trans('Current project quota cannot be adjusted to new subscription plan')));

        throw_if($subscriptionPlan->getAllowedCollaborators() < $user->getActiveCollaborators(),
            new SubscriptionDowngradeRequirementException(trans('Current collaborator quota cannot be adjusted to new subscription plan')));

        return true;
    }
}
