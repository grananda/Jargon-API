<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;

class SendPasswordRecoveryEmail extends Mailable
{
    /**
     * @var \App\Models\User
     */
    private $user;

    /**
     * @var string
     */
    public $token;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\User $user
     * @param string           $token
     */
    public function __construct(User $user, string $token)
    {
        $this->user = $user;

        $this->token = $token;
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
            'token' => $this->token,
        ]);

        return $this->view('emails.auth.passwordRecovery')
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
