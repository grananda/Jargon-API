<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ActiveSubscription\UpgradeActiveSubscriptionRequest;
use App\Http\Resources\ActiveSubscription\ActiveSubscription as ActiveSubscriptionResource;
use App\Services\SubscriptionService;
use Exception;

class ActiveSubscriptionUpgradeController extends ApiController
{
    /**
     * @var \App\Services\SubscriptionService
     */
    private $subscriptionService;

    /**
     * ActiveSubscriptionUpgradeController constructor.
     *
     * @param \App\Services\SubscriptionService $subscriptionService
     */
    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
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
            /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
            $activeSubscription = $this->subscriptionService->subscribe($request->user(), $request->subscriptionPlan);

            return $this->responseOk(new ActiveSubscriptionResource($activeSubscription));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
