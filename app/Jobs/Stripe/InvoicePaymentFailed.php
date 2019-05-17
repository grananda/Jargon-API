<?php

namespace App\Jobs\Stripe;

use App\Mail\SendSubscriptionDeactivationEmail;
use App\Repositories\ActiveSubscriptionRepository;
use Illuminate\Support\Facades\Mail;

class InvoicePaymentFailed extends AbstractStripeJob
{
    /**
     * Execute the job.
     *
     * @param \App\Repositories\ActiveSubscriptionRepository $activeSubscriptionRepository
     *
     * @return void
     */
    public function handle(ActiveSubscriptionRepository $activeSubscriptionRepository)
    {
        $subscriptionId = $this->data['lines']['data']['0']['subscription'];

        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $activeSubscriptionRepository->findByOrFail(['stripe_id' => $subscriptionId]);

        if ($activeSubscription->isSubscriptionActive()) {
            $activeSubscription->deactivate();

            Mail::to($activeSubscription->user)
                ->send(new SendSubscriptionDeactivationEmail($activeSubscription))
            ;
        }
    }
}
