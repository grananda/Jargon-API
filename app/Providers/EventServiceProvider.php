<?php

namespace App\Providers;

use App\Events\Collaborator\CollaboratorAddedToProject;
use App\Events\Collaborator\CollaboratorAddedToTeam;
use App\Events\Option\OptionWasCreated;
use App\Events\Option\OptionWasDeleted;
use App\Events\SubscriptionPlan\SubscriptionPlanWasCreated;
use App\Events\SubscriptionPlan\SubscriptionPlanWasDeleted;
use App\Events\SubscriptionPlan\SubscriptionPlanWasUpdated;
use App\Events\User\UserActivationTokenGenerated;
use App\Events\User\UserWasActivated;
use App\Events\User\UserWasDeactivated;
use App\Events\User\UserWasDeleted;
use App\Listeners\AddOptionUser;
use App\Listeners\CreateStripeSubscriptionPlan;
use App\Listeners\DeactivateActiveSubscription;
use App\Listeners\DeleteOptionUser;
use App\Listeners\DeleteStripeSubscriptionPlan;
use App\Listeners\InitializeActiveSubscription;
use App\Listeners\InitializeUserOptions;
use App\Listeners\SendProjectCollaboratorNotification;
use App\Listeners\SendTeamCollaboratorNotification;
use App\Listeners\SendUserActivationNotification;
use App\Listeners\SendUserDeactivationNotification;
use App\Listeners\SendUserDeletionNotification;
use App\Listeners\UpdateStripeSubscriptionPlan;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // User events
        UserActivationTokenGenerated::class => [
            SendUserActivationNotification::class,
        ],
        UserWasActivated::class => [
            InitializeActiveSubscription::class,
            InitializeUserOptions::class,
        ],
        UserWasDeactivated::class => [
            DeactivateActiveSubscription::class,
            SendUserDeactivationNotification::class,
        ],
        UserWasDeleted::class => [
            SendUserDeletionNotification::class,
        ],

        // Collaborator events
        CollaboratorAddedToTeam::class => [
            SendTeamCollaboratorNotification::class,
        ],
        CollaboratorAddedToProject::class => [
            SendProjectCollaboratorNotification::class,
        ],

        // Option events
        OptionWasCreated::class => [
            AddOptionUser::class,
        ],
        OptionWasDeleted::class => [
            DeleteOptionUser::class,
        ],

        // SubscriptionPlan events
        SubscriptionPlanWasCreated::class => [
            CreateStripeSubscriptionPlan::class,
        ],
        SubscriptionPlanWasDeleted::class => [
            DeleteStripeSubscriptionPlan::class,
        ],
        SubscriptionPlanWasUpdated::class => [
            UpdateStripeSubscriptionPlan::class,
        ],
    ];
}
