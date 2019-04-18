<?php

namespace App\Services;

use App\Exceptions\ActiveSubscriptionStatusException;
use App\Exceptions\StripeCardTokenMissingException;
use App\Models\Subscriptions\ActiveSubscription;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\User;
use App\Repositories\ActiveSubscriptionRepository;
use App\Repositories\Stripe\StripeSubscriptionRepository;
use App\Services\Contract\SubscriptionServiceInterface;

class SubscriptionService implements SubscriptionServiceInterface
{
    /**
     * The CustomerService instance.
     *
     * @var \App\Services\CustomerService
     */
    private $customerService;

    /**
     * @var \App\Services\CardService
     */
    private $cardService;

    /**
     * The ActiveSubscriptionRepository instance.
     *
     * @var \App\Repositories\ActiveSubscriptionRepository
     */
    private $activeSubscriptionRepository;

    /**
     * The StripeSubscriptionRepository instance.
     *
     * @var \App\Repositories\Stripe\StripeSubscriptionRepository
     */
    private $stripeSubscriptionRepository;

    /**
     * SubscriptionService constructor.
     *
     * @param \App\Repositories\ActiveSubscriptionRepository        $activeSubscriptionRepository
     * @param \App\Repositories\Stripe\StripeSubscriptionRepository $stripeSubscriptionRepository
     * @param \App\Services\CustomerService                         $customerService
     * @param \App\Services\CardService                             $cardService
     */
    public function __construct(ActiveSubscriptionRepository $activeSubscriptionRepository, StripeSubscriptionRepository $stripeSubscriptionRepository, CustomerService $customerService, CardService $cardService)
    {
        $this->activeSubscriptionRepository = $activeSubscriptionRepository;
        $this->stripeSubscriptionRepository = $stripeSubscriptionRepository;
        $this->customerService              = $customerService;
        $this->cardService                  = $cardService;
    }

    /** {@inheritdoc} */
    public function subscribe(User $user, SubscriptionPlan $subscriptionPlan, string $stripeToken = null): ActiveSubscription
    {
        if (! $user->isStripeCustomer()) {
            /** @var User $user */
            $user = $this->customerService->registerCustomer($user);
        }

        if (! $user->hasCard()) {
            throw_if(is_null($stripeToken), new StripeCardTokenMissingException(trans('Subscription upgrade requires of Stripe card token')));

            $this->cardService->registerCard($user, $stripeToken);
        }

        if (! (bool) $user->activeSubscription || ! (bool) $user->activeSubscription->stripe_id) {
            return $this->createSubscription($user, $subscriptionPlan);
        }

        return $this->swapSubscription($user, $subscriptionPlan);
    }

    /** {@inheritdoc} */
    public function cancelSubscription(User $user, ActiveSubscription $activeSubscription): ActiveSubscription
    {
        throw_if(! $activeSubscription->isSubscriptionActive(), new ActiveSubscriptionStatusException(trans('The user current subscription can not be canceled.')));

        /** @var array $stripeResponse */
        $stripeResponse = $this->stripeSubscriptionRepository->cancel($user, $activeSubscription);

        return $activeSubscription->deactivate($stripeResponse['cancel_at']);
    }

    /** {@inheritdoc} */
    public function reactivateSubscription(User $user, ActiveSubscription $activeSubscription): ActiveSubscription
    {
        throw_if($activeSubscription->isSubscriptionActive(), new ActiveSubscriptionStatusException(trans('The user current subscription can not be canceled.')));

        $this->stripeSubscriptionRepository->reactivate($user, $activeSubscription);

        return $activeSubscription->activate();
    }

    /**
     * Creates a new subscription.
     *
     * @param \App\Models\User                           $user
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan
     *
     * @throws \Throwable
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return \App\Models\Subscriptions\ActiveSubscription|void
     */
    private function createSubscription(User $user, SubscriptionPlan $subscriptionPlan)
    {
        /** @var array $stripeSubscription */
        $stripeSubscription = $this->stripeSubscriptionRepository->create($user, $subscriptionPlan);

        if ((bool) $user->activeSubscription) {
            return $this->activeSubscriptionRepository->updateActiveSubscription($subscriptionPlan, $user, [
                'stripe_id' => $stripeSubscription['id'],
            ]);
        }

        return $this->activeSubscriptionRepository->createActiveSubscription($subscriptionPlan, $user, [
            'stripe_id' => $stripeSubscription['id'],
        ]);
    }

    /**
     * Swaps current subscription to another subscription.
     *
     * @param \App\Models\User                           $user
     * @param \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan
     *
     * @throws \App\Exceptions\StripeApiCallException
     * @throws \Throwable
     *
     * @return \App\Models\Subscriptions\ActiveSubscription|void
     */
    private function swapSubscription(User $user, SubscriptionPlan $subscriptionPlan)
    {
        throw_if(! $user->activeSubscription->isSubscriptionActive(), new ActiveSubscriptionStatusException(trans('The user current subscription can not be canceled.')));

        /** @var array $stripeSubscription */
        $stripeSubscription = $this->stripeSubscriptionRepository->swap($user, $subscriptionPlan);

        return $this->activeSubscriptionRepository->updateActiveSubscription($subscriptionPlan, $user, [
            'stripe_id' => $stripeSubscription['id'],
        ]);
    }
}
