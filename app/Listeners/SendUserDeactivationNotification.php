<?php

namespace App\Listeners;

use App\Events\User\UserWasDeactivated;
use App\Mail\SendUserDeactivationEmail;
use Illuminate\Support\Facades\Mail;

class SendUserDeactivationNotification
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
        Mail::to($event->user)
            ->send(new SendUserDeactivationEmail($event->user))
        ;
    }
}
