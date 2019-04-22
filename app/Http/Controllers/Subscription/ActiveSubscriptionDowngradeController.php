<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ActiveSubscription\DowngradeActiveSubscriptionRequest;
use App\Http\Resources\ActiveSubscription\ActiveSubscription as ActiveSubscriptionResource;
use App\Services\SubscriptionDowngradeService;
use App\Services\SubscriptionService;
use Exception;

class ActiveSubscriptionDowngradeController extends ApiController
{
    /**
     * @var \App\Services\SubscriptionService
     */
    private $subscriptionService;

    /**
     * @var \App\Services\SubscriptionDowngradeService
     */
    private $subscriptionDowngradeService;

    /**
     * ActiveSubscriptionUpgradeController constructor.
     *
     * @param \App\Services\SubscriptionService          $subscriptionService
     * @param \App\Services\SubscriptionDowngradeService $subscriptionDowngradeService
     */
    public function __construct(SubscriptionService $subscriptionService, SubscriptionDowngradeService $subscriptionDowngradeService)
    {
        $this->subscriptionService          = $subscriptionService;
        $this->subscriptionDowngradeService = $subscriptionDowngradeService;
    }

    /**
     * Updates current user active subscription.
     *
     * @param \App\Http\Requests\ActiveSubscription\DowngradeActiveSubscriptionRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(DowngradeActiveSubscriptionRequest $request)
    {
        try {
            $this->subscriptionDowngradeService->checkSubscriptionPlanDowngradeRules($request->user(), $request->subscriptionPlan);

            /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
            $activeSubscription = $this->subscriptionService->subscribe($request->user(), $request->subscriptionPlan);

            return $this->responseOk(new ActiveSubscriptionResource($activeSubscription));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
