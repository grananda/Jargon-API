<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;

class SendUserActivationEmail extends Mailable
{
    /**
     * User recipient.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * SendUserActivationNotification constructor.
     *
     * @param \App\Models\User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $app     = env('APP_NAME');
        $subject = trans(':app Account Activation', ['app' => $app]);
        $link    = route('auth.activate', [
            'id'    => $this->user->uuid,
            'token' => $this->user->activation_token,
        ]);

        // TODO: Complete email view

        return $this->view('emails.auth.userActivation')
            ->subject($subject)
            ->with([
                'app'     => $app,
                'subject' => $subject,
                'user'    => $this->user,
                'link'    => $link,
            ])
        ;
    }
}
