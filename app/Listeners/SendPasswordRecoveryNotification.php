<?php

namespace App\Listeners;

use App\Events\User\PasswordResetRequested;
use App\Mail\SendPasswordRecoveryEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendPasswordRecoveryNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param \App\Events\User\PasswordResetRequested $event
     *
     * @return void
     */
    public function handle(PasswordResetRequested $event)
    {
        Mail::to($event->user)
            ->send(new SendPasswordRecoveryEmail($event->user, $event->token))
        ;
    }
}
