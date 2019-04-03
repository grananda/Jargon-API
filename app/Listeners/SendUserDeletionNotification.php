<?php

namespace App\Listeners;

use App\Events\User\UserWasDeleted;
use App\Mail\SendUserDeletionEmail;
use Illuminate\Support\Facades\Mail;

class SendUserDeletionNotification
{
    /**
     * Handle the event.
     *
     * @param \App\Events\User\UserWasDeleted $event
     *
     * @return void
     */
    public function handle(UserWasDeleted $event)
    {
        Mail::to($event->user)
            ->send(new SendUserDeletionEmail($event->user))
        ;
    }
}
