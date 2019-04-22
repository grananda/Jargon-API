<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Resources\Users\User as UserResource;
use App\Services\CustomerService;
use Exception;

class CustomerController extends ApiController
{
    /**
     * The CustomerService instance.
     *
     * @var \App\Services\CustomerService
     */
    private $customerService;

    /**
     * CustomerController constructor.
     *
     * @param \App\Services\CustomerService $customerService
     */
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Customer\StoreCustomerRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCustomerRequest $request)
    {
        try {
            /** @var \App\Models\User $user */
            $user = $this->customerService->registerCustomer($request->user());

            return $this->responseOk(new UserResource($user));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
