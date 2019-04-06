<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ActiveSubscription\DowngradeActiveSubscriptionRequest;
use Exception;

class ActiveSubscriptionDowngradeController extends ApiController
{
    /**
     * Updates current user active subscription.
     *
     * @param \App\Http\Requests\ActiveSubscription\DowngradeActiveSubscriptionRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(DowngradeActiveSubscriptionRequest $request)
    {
        try {
            return $this->responseNoContent();
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
