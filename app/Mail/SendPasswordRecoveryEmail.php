<?php

namespace App\Mail;

use App\Models\PasswordReset;
use Illuminate\Mail\Mailable;

class SendPasswordRecoveryEmail extends Mailable
{
    /**
     * @var \App\Models\PasswordReset
     */
    private $passwordReset;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\PasswordReset $passwordReset
     */
    public function __construct(PasswordReset $passwordReset)
    {
        $this->passwordReset = $passwordReset;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $app     = env('APP_NAME');
        $subject = trans(':app Password Recovery Request', ['app' => $app]);
        $link    = route('account.password.reset', [
            'token' => $this->passwordReset->token,
        ]);

        return $this->view('emails.auth.passwordRecovery')
            ->subject($subject)
            ->with([
                'app'     => $app,
                'subject' => $subject,
                'email'   => $this->passwordReset->email,
                'link'    => $link,
            ])
        ;
    }
}
