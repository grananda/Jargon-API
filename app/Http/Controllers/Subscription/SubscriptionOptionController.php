<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\SubscriptionOption\DeleteSubscriptionOptionRequest;
use App\Http\Requests\SubscriptionOption\IndexSubscriptionOptionRequest;
use App\Http\Requests\SubscriptionOption\StoreSubscriptionOptionRequest;
use App\Http\Requests\SubscriptionOption\UpdateSubscriptionOptionRequest;
use App\Http\Resources\SubscriptionOption\SubscriptionOption as SubscriptionPlanOptionResource;
use App\Http\Resources\SubscriptionOption\SubscriptionOptionCollection;
use App\Repositories\SubscriptionOptionRepository;
use Exception;

class SubscriptionOptionController extends ApiController
{
    /**
     * The OptionRepository instance.
     *
     * @var \App\Repositories\SubscriptionOptionRepository
     */
    private $subscriptionOption;

    /**
     * SubscriptionPlanOptionController constructor.
     *
     * @param \App\Repositories\SubscriptionOptionRepository $subscriptionPlanOption
     */
    public function __construct(SubscriptionOptionRepository $subscriptionPlanOption)
    {
        $this->subscriptionOption = $subscriptionPlanOption;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\SubscriptionOption\IndexSubscriptionOptionRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexSubscriptionOptionRequest $request)
    {
        try {
            $subscriptionOptions = $this->subscriptionOption->findAllBy([]);

            return $this->responseOk(new SubscriptionOptionCollection($subscriptionOptions));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\SubscriptionOption\StoreSubscriptionOptionRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreSubscriptionOptionRequest $request)
    {
        try {
            $subscriptionOption = $this->subscriptionOption->create($request->validated());

            return $this->responseCreated(new SubscriptionPlanOptionResource($subscriptionOption));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\SubscriptionOption\UpdateSubscriptionOptionRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSubscriptionOptionRequest $request)
    {
        try {
            $subscriptionOption = $this->subscriptionOption->update($request->subscriptionOption, $request->validated());

            return $this->responseOk(new SubscriptionPlanOptionResource($subscriptionOption));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\SubscriptionOption\DeleteSubscriptionOptionRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteSubscriptionOptionRequest $request)
    {
        try {
            $this->subscriptionOption->delete($request->subscriptionOption);

            return $this->responseNoContent();
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
