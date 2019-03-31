<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\SubscriptionPlan\DeleteSubscriptionPlanRequest;
use App\Http\Requests\SubscriptionPlan\IndexSubscriptionPlanRequest;
use App\Http\Requests\SubscriptionPlan\ShowSubscriptionPlanRequest;
use App\Http\Requests\SubscriptionPlan\StoreSubscriptionPlanRequest;
use App\Http\Requests\SubscriptionPlan\UpdateSubscriptionPlanRequest;
use App\Http\Resources\Subscriptions\SubscriptionPlan as SubscriptionPlanResource;
use App\Http\Resources\Subscriptions\SubscriptionPlanCollection;
use App\Repositories\SubscriptionPlanRepository;
use Exception;

class SubscriptionPlanController extends ApiController
{
    /**
     * The SubscriptionPlanRepository instance.
     *
     * @var \App\Repositories\SubscriptionPlanRepository
     */
    private $subscriptionPlanRepository;

    /**
     * SubscriptionPlanController constructor.
     *
     * @param \App\Repositories\SubscriptionPlanRepository $subscriptionPlanRepository
     */
    public function __construct(SubscriptionPlanRepository $subscriptionPlanRepository)
    {
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\SubscriptionPlan\IndexSubscriptionPlanRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexSubscriptionPlanRequest $request)
    {
        try {
            $organizations = $this->subscriptionPlanRepository->findAllBy(['status' => true]);

            return $this->responseOk(new SubscriptionPlanCollection($organizations));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\SubscriptionPlan\StoreSubscriptionPlanRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreSubscriptionPlanRequest $request)
    {
        try {
            $subscriptionPlan = $this->subscriptionPlanRepository->createSubscriptionPlan($request->validated());

            return $this->responseCreated(new SubscriptionPlanResource($subscriptionPlan));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\SubscriptionPlan\ShowSubscriptionPlanRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowSubscriptionPlanRequest $request)
    {
        try {
            return $this->responseOk(new SubscriptionPlanResource($request->subscriptionPlan));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\SubscriptionPlan\UpdateSubscriptionPlanRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSubscriptionPlanRequest $request)
    {
        try {
            $subscriptionPlan = $this->subscriptionPlanRepository->updateSubscriptionPlan($request->subscriptionPlan, $request->validated());

            return $this->responseOk(new SubscriptionPlanResource($subscriptionPlan));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\SubscriptionPlan\DeleteSubscriptionPlanRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteSubscriptionPlanRequest $request)
    {
        try {
            $this->subscriptionPlanRepository->delete($request->subscriptionPlan);

            return $this->responseNoContent();
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
