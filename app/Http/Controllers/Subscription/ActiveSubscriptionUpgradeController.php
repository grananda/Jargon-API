<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ActiveSubscription\UpgradeActiveSubscriptionRequest;
use Exception;

class ActiveSubscriptionUpgradeController extends ApiController
{
    /**
     * Updates current user active subscription.
     *
     * @param \App\Http\Requests\ActiveSubscription\UpgradeActiveSubscriptionRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpgradeActiveSubscriptionRequest $request)
    {
        try {
            return $this->responseNoContent();
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
