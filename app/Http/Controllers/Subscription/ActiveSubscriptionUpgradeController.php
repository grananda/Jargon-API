<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ActiveSubscription\UpgradeActiveSubscriptionRequest;
use App\Repositories\ActiveSubscriptionRepository;
use Exception;

class ActiveSubscriptionUpgradeController extends ApiController
{
    /**
     * @var \App\Repositories\ActiveSubscriptionRepository
     */
    private $activeSubscriptionRepository;

    /**
     * ActiveSubscriptionUpgradeController constructor.
     *
     * @param \App\Repositories\ActiveSubscriptionRepository $activeSubscriptionRepository
     */
    public function __construct(ActiveSubscriptionRepository $activeSubscriptionRepository)
    {
        $this->activeSubscriptionRepository = $activeSubscriptionRepository;
    }

    /**
     * Updates current user active subscription.
     *
     * @param \App\Http\Requests\ActiveSubscription\UpgradeActiveSubscriptionRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpgradeActiveSubscriptionRequest $request)
    {
        try {
            $this->activeSubscriptionRepository->updateActiveSubscription($request->subscriptionPlan, $request->user());

            return $this->responseNoContent();
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
