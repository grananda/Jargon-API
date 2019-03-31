<?php

namespace App\Providers;

use App\Events\Collaborator\CollaboratorAddedToProject;
use App\Events\Collaborator\CollaboratorAddedToTeam;
use App\Events\Option\OptionWasCreated;
use App\Events\Option\OptionWasDeleted;
use App\Events\SubscriptionOption\SubscriptionOptionWasCreated;
use App\Events\SubscriptionOption\SubscriptionOptionWasDeleted;
use App\Listeners\AddOptionUser;
use App\Listeners\AddSubscriptionPlanOptionValue;
use App\Listeners\DeleteOptionUser;
use App\Listeners\DeleteSubscriptionPlanOptionValue;
use App\Listeners\SendProjectCollaboratorInvitationEmail;
use App\Listeners\SendTeamCollaboratorInvitationEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
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

        // SubscriptionOption events
        SubscriptionOptionWasCreated::class => [
            AddSubscriptionPlanOptionValue::class,
        ],
        SubscriptionOptionWasDeleted::class => [
            DeleteSubscriptionPlanOptionValue::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
