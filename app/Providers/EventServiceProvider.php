<?php

namespace App\Providers;

use App\Events\ActiveSubscription\ActiveSubscriptionWasActivated;
use App\Events\ActiveSubscription\ActiveSubscriptionWasDeactivated;
use App\Events\Collaborator\CollaboratorAddedToProject;
use App\Events\Collaborator\CollaboratorAddedToTeam;
use App\Events\Option\OptionWasCreated;
use App\Events\Option\OptionWasDeleted;
use App\Events\SubscriptionPlan\SubscriptionPlanWasCreated;
use App\Events\SubscriptionPlan\SubscriptionPlanWasDeleted;
use App\Events\SubscriptionPlan\SubscriptionPlanWasUpdated;
use App\Events\SubscriptionProduct\SubscriptionProductWasCreated;
use App\Events\SubscriptionProduct\SubscriptionProductWasDeleted;
use App\Events\SubscriptionProduct\SubscriptionProductWasUpdated;
use App\Events\User\UserActivationTokenGenerated;
use App\Events\User\UserWasActivated;
use App\Events\User\UserWasDeactivated;
use App\Events\User\UserWasDeleted;
use App\Listeners\AddOptionUser;
use App\Listeners\CancelStripeSubscription;
use App\Listeners\DeactivateActiveSubscription;
use App\Listeners\DeleteOptionUser;
use App\Listeners\DeleteStripeCustomer;
use App\Listeners\InitializeActiveSubscription;
use App\Listeners\InitializeUserOptions;
use App\Listeners\ReactivateStripeSubscription;
use App\Listeners\SendProjectCollaboratorNotification;
use App\Listeners\SendTeamCollaboratorNotification;
use App\Listeners\SendUserActivationNotification;
use App\Listeners\SendUserDeactivationNotification;
use App\Listeners\SendUserDeletionNotification;
use App\Listeners\SubscriptionPlans\DeleteStripeSubscriptionPlan;
use App\Listeners\SubscriptionPlans\UpdateStripeSubscriptionPlan;
use App\Listeners\SubscriptionProducts\CreateStripeSubscriptionProduct;
use App\Listeners\SubscriptionProducts\DeleteStripeSubscriptionProduct;
use App\Listeners\SubscriptionProducts\UpdateStripeSubscriptionProduct;
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
            DeleteStripeCustomer::class,
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

        // SubscriptionProduct events
        SubscriptionProductWasCreated::class => [
            CreateStripeSubscriptionProduct::class,
        ],
        SubscriptionProductWasDeleted::class => [
            DeleteStripeSubscriptionProduct::class,
        ],
        SubscriptionProductWasUpdated::class => [
            UpdateStripeSubscriptionProduct::class,
        ],

        // SubscriptionPlan events
        SubscriptionPlanWasCreated::class => [
            CreateStripeSubscriptionProduct::class,
        ],
        SubscriptionPlanWasDeleted::class => [
            DeleteStripeSubscriptionPlan::class,
        ],
        SubscriptionPlanWasUpdated::class => [
            UpdateStripeSubscriptionPlan::class,
        ],

        // ActiveSubscription
        ActiveSubscriptionWasActivated::class => [
            ReactivateStripeSubscription::class,
        ],
        ActiveSubscriptionWasDeactivated::class => [
            CancelStripeSubscription::class,
        ],
    ];
}
