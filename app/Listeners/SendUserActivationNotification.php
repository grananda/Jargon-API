<?php

namespace App\Listeners;

use App\Events\User\UserActivationTokenGenerated;
use App\Mail\SendUserActivationEmail;
use Illuminate\Support\Facades\Mail;

class SendUserActivationNotification
{
    /**
     * Handle the event.
     *
     * @param \App\Events\User\UserActivationTokenGenerated $event
     *
     * @return void
     */
    public function handle(UserActivationTokenGenerated $event)
    {
        Mail::to($event->user)
            ->send(new SendUserActivationEmail($event->user))
        ;
    }
}
