<?php

namespace App\Policies\Traits;

use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\User;

trait ActiveSubscriptionRestrictionsTrait
{
    /**
     * Determines if current entity can hold desired collaborator count.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function getCurrentSubscriptionCollaborationQuota(User $user)
    {
        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        if ($activeSubscription = $user->activeSubscription) {
            $subscriptionCollaboratorQuota = $activeSubscription->getAllowedCollaborators();

            $subscriptionCollaboratorQuota -= $user->getOrganizationCollaboratorCount();
            $subscriptionCollaboratorQuota -= $user->getTeamCollaboratorCount();
            $subscriptionCollaboratorQuota -= $user->getProjectCollaboratorCount();

            return $subscriptionCollaboratorQuota;
        }

        return false;
    }

    /**
     * Returns user subscription organization quota.
     *
     * @param \App\Models\User $user
     *
     * @return int
     */
    public function getCurrentSubscriptionOrganizationQuota(User $user)
    {
        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        if ($activeSubscription = $user->activeSubscription) {
            $subscriptionProjectQuota = $activeSubscription->getAllowedOrganizations();
            $currentOrganizationCount = $user->getActiveOrganizations();

            $subscriptionProjectQuota -= $currentOrganizationCount;

            return $subscriptionProjectQuota;
        }

        return false;
    }

    /**
     * Returns user subscription team quota.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function getCurrentSubscriptionTeamQuota(User $user)
    {
        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        if ($activeSubscription = $user->activeSubscription) {
            $subscriptionTeamQuota = $activeSubscription->getAllowedTeams();
            $currentTeamCount      = $user->getActiveTeams();

            $subscriptionTeamQuota -= $currentTeamCount;

            return $subscriptionTeamQuota;
        }

        return false;
    }

    /**
     * Returns user subscription project quota.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function getCurrentSubscriptionProjectQuota(User $user)
    {
        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        if ($activeSubscription = $user->activeSubscription) {
            $subscriptionProjectQuota = $activeSubscription->getAllowedProjects();
            $currentProjectQuota      = $user->getActiveProjects();

            $subscriptionProjectQuota -= $currentProjectQuota;

            return $subscriptionProjectQuota;
        }

        return false;
    }

    /**
     * Determines if user has current subscription.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function hasActiveSubscription(User $user)
    {
        return (bool) $user->activeSubscription && $user->activeSubscription->isSubscriptionActive();
    }

    /**
     * Determines if a subscription can be upgraded.
     *
     * @param \App\Models\User                           $user
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan
     *
     * @return bool
     */
    public function canUpgrade(User $user, SubscriptionPlan $subscriptionPlan)
    {
        return $user->activeSubscription->subscriptionPlan->product->rank < $subscriptionPlan->product->rank;
    }

    /**
     * Determines if a subscription can be donwgraded.
     *
     * @param \App\Models\User                           $user
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan
     *
     * @return bool
     */
    public function canDowngrade(User $user, SubscriptionPlan $subscriptionPlan)
    {
        return $user->activeSubscription->subscriptionPlan->product->rank > $subscriptionPlan->product->rank;
    }
}
