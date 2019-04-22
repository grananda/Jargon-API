<?php

namespace App\Http\Controllers\Subscription;

use App\Exceptions\SubscriptionProductDeleteException;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\SubscriptionProduct\DeleteSubscriptionProductRequest;
use App\Http\Requests\SubscriptionProduct\IndexSubscriptionProductRequest;
use App\Http\Requests\SubscriptionProduct\ShowSubscriptionProductRequest;
use App\Http\Requests\SubscriptionProduct\StoreSubscriptionProductRequest;
use App\Http\Requests\SubscriptionProduct\UpdateSubscriptionProductRequest;
use App\Http\Resources\SubscriptionProduct\SubscriptionProduct as SubscriptionProductResource;
use App\Http\Resources\SubscriptionProduct\SubscriptionProductCollection;
use App\Repositories\SubscriptionProductRepository;
use Exception;

class SubscriptionProductController extends ApiController
{
    /**
     * @var \App\Repositories\SubscriptionProductRepository
     */
    private $subscriptionProductRepository;

    /**
     * SubscriptionProductController constructor.
     *
     * @param \App\Repositories\SubscriptionProductRepository $subscriptionProductRepository
     */
    public function __construct(SubscriptionProductRepository $subscriptionProductRepository)
    {
        $this->subscriptionProductRepository = $subscriptionProductRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\SubscriptionProduct\IndexSubscriptionProductRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexSubscriptionProductRequest $request)
    {
        try {
            $plans = $this->subscriptionProductRepository->findAllBy(['is_active' => true]);

            return $this->responseOk(new SubscriptionProductCollection($plans));
        } catch (Exception $exception) {
            return $this->responseInternalError($exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\SubscriptionProduct\StoreSubscriptionProductRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreSubscriptionProductRequest $request)
    {
        try {
            $subscriptionPlan = $this->subscriptionProductRepository->create($request->validated());

            return $this->responseCreated(new SubscriptionProductResource($subscriptionPlan));
        } catch (Exception $exception) {
            return $this->responseInternalError($exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\SubscriptionProduct\ShowSubscriptionProductRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowSubscriptionProductRequest $request)
    {
        try {
            return $this->responseOk(new SubscriptionProductResource($request->subscriptionProduct));
        } catch (Exception $exception) {
            return $this->responseInternalError($exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\SubscriptionProduct\UpdateSubscriptionProductRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSubscriptionProductRequest $request)
    {
        try {
            $subscriptionPlan = $this->subscriptionProductRepository->update($request->subscriptionProduct, $request->validated());

            return $this->responseOk(new SubscriptionProductResource($subscriptionPlan));
        } catch (Exception $exception) {
            return $this->responseInternalError($exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\SubscriptionProduct\DeleteSubscriptionProductRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteSubscriptionProductRequest $request)
    {
        try {
            $this->subscriptionProductRepository->deleteSubscriptionProduct($request->subscriptionProduct);

            return $this->responseNoContent();
        } catch (SubscriptionProductDeleteException $subscriptionPlanDeleteException) {
            return $this->responseInternalError($subscriptionPlanDeleteException->getMessage());
        } catch (Exception $exception) {
            return $this->responseInternalError($exception->getMessage());
        }
    }
}
