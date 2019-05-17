<?php

namespace App\Listeners;

use App\Events\User\UserWasDeactivated;

class DeactivateActiveSubscription
{
    /**
     * Handle the event.
     *
     * @param \App\Events\User\UserWasDeactivated $event
     *
     * @return void
     */
    public function handle(UserWasDeactivated $event)
    {
        if ((bool) $event->user->activeSubscription()) {
            $event->user->activeSubscription->deactivate();
        }
    }
}
