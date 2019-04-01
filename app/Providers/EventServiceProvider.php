<?php

namespace App\Providers;

use App\Events\Collaborator\CollaboratorAddedToProject;
use App\Events\Collaborator\CollaboratorAddedToTeam;
use App\Events\Option\OptionWasCreated;
use App\Events\Option\OptionWasDeleted;
use App\Events\User\UserActivationTokenGenerated;
use App\Events\User\UserWasActivated;
use App\Events\User\UserWasDeactivated;
use App\Events\User\UserWasDeleted;
use App\Listeners\AddOptionUser;
use App\Listeners\DeleteOptionUser;
use App\Listeners\InitializeActiveSubscription;
use App\Listeners\InitializeUserOptions;
use App\Listeners\SendEmailActivationNotification;
use App\Listeners\SendProjectCollaboratorInvitationEmail;
use App\Listeners\SendTeamCollaboratorInvitationEmail;
use App\Listeners\SendUserActivationEmail;
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
            SendUserActivationEmail::class,
        ],
        UserWasActivated::class => [
            InitializeActiveSubscription::class,
            InitializeUserOptions::class,
            SendEmailActivationNotification::class,
        ],
        UserWasDeactivated::class => [
            SendEmailDeactivationNotification::class,
        ],
        UserWasDeleted::class => [
            SendEmailDeletionNotification::class,
        ],

        // Collaborator events
        CollaboratorAddedToTeam::class => [
            SendTeamCollaboratorInvitationEmail::class,
        ],
        CollaboratorAddedToProject::class => [
            SendProjectCollaboratorInvitationEmail::class,
        ],

        // Option events
        OptionWasCreated::class => [
            AddOptionUser::class,
        ],
        OptionWasDeleted::class => [
            DeleteOptionUser::class,
        ],
    ];
}
