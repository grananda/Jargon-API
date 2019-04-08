<?php

namespace App\Listeners;

use App\Events\User\UserWasActivated;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Repositories\ActiveSubscriptionRepository;
use App\Repositories\SubscriptionPlanRepository;

class InitializeActiveSubscription
{
    /**
     * @var \App\Repositories\SubscriptionPlanRepository
     */
    private $subscriptionPlanRepository;

    /**
     * @var \App\Repositories\ActiveSubscriptionRepository;
     */
    private $activeSubscriptionRepository;

    /**
     * InitializeActiveSubscription constructor.
     *
     * @param \App\Repositories\SubscriptionPlanRepository   $subscriptionPlanRepository
     * @param \App\Repositories\ActiveSubscriptionRepository $activeSubscriptionRepository
     */
    public function __construct(SubscriptionPlanRepository $subscriptionPlanRepository,
                                ActiveSubscriptionRepository $activeSubscriptionRepository)
    {
        $this->subscriptionPlanRepository   = $subscriptionPlanRepository;
        $this->activeSubscriptionRepository = $activeSubscriptionRepository;
    }

    /**
     * Handle the event.
     *
     * @param \App\Events\User\UserWasActivated $event
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function handle(UserWasActivated $event)
    {
        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscription */
        $subscription = $this->subscriptionPlanRepository->findBy(['alias' => SubscriptionPlan::DEFAULT_SUBSCRIPTION_PLAN]);

        if ($event->user->activeSubscription) {
            $event->user->activeSubscription->activate();
        } else {
            $this->activeSubscriptionRepository->createActiveSubscription($subscription, $event->user);
        }
    }
}
