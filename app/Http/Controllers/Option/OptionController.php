<?php

namespace App\Http\Controllers\Option;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Option\DeleteOptionRequest;
use App\Http\Requests\Option\IndexOptionRequest;
use App\Http\Requests\Option\StoreOptionRequest;
use App\Http\Requests\Option\UpdateOptionRequest;
use App\Http\Resources\Options\Option as OptionResource;
use App\Http\Resources\Options\OptionCollection;
use App\Repositories\OptionRepository;
use Exception;

class OptionController extends ApiController
{
    /**
     * The OptionRepository instance.
     *
     * @var \App\Repositories\OptionRepository
     */
    private $optionRepository;

    /**
     * SubscriptionPlanOptionController constructor.
     *
     * @param \App\Repositories\OptionRepository $optionRepository
     */
    public function __construct(OptionRepository $optionRepository)
    {
        $this->optionRepository = $optionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\Option\IndexOptionRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexOptionRequest $request)
    {
        try {
            $options = $this->optionRepository->findAllBy([]);

            return $this->responseOk(new OptionCollection($options));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Option\StoreOptionRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOptionRequest $request)
    {
        try {
            $subscriptionPlan = $this->optionRepository->create($request->validated());

            return $this->responseCreated(new OptionResource($subscriptionPlan));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Option\UpdateOptionRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateOptionRequest $request)
    {
        try {
            $subscriptionPlan = $this->optionRepository->update($request->option, $request->validated());

            return $this->responseOk(new OptionResource($subscriptionPlan));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\Option\DeleteOptionRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteOptionRequest $request)
    {
        try {
            $this->optionRepository->delete($request->option);

            return $this->responseNoContent();
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
