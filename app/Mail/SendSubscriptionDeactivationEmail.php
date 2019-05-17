<?php

namespace App\Mail;

use App\Models\Subscriptions\ActiveSubscription;
use Illuminate\Mail\Mailable;

class SendSubscriptionDeactivationEmail extends Mailable
{
    /**
     * @var \App\Models\Subscriptions\ActiveSubscription
     */
    private $activeSubscription;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Subscriptions\ActiveSubscription $activeSubscription
     */
    public function __construct(ActiveSubscription $activeSubscription)
    {
        $this->activeSubscription = $activeSubscription;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $app     = env('APP_NAME');
        $subject = trans(':app Subscription Deactivated', ['app' => $app]);

        // TODO: Complete email view

        return $this->view('emails.subscription.subscriptionDeactivated')
            ->subject($subject)
            ->with([
                'app'                => $app,
                'subject'            => $subject,
                'activeSubscription' => $this->activeSubscription,
            ])
        ;
    }
}
