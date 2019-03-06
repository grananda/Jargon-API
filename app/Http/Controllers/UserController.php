<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\UserRegistrationRequest;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\Request;

class UserController extends ApiController
{
    /**
     * The UserRepository instance.
     *
     * @var \App\Repositories\UserRepository
     */
    protected $userRepository;

    /**
     * Constructor.
     *
     * @param \App\Repositories\UserRepository $loginRepository
     */
    public function __construct(UserRepository $loginRepository)
    {
        $this->userRepository = $loginRepository;
    }

    /**
     * Registers user.
     *
     * @param \App\Http\Requests\UserRegistrationRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserRegistrationRequest $request)
    {
        try {
            $this->userRepository->create($request->validated());

            return $this->responseCreated(trans('Successfully created user'));
        } catch (Exception $exception) {
            return $this->responseInternalError($exception->getMessage());
        }
    }

    /**
     * Displays current user information.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        return $this->responseOk($request->user());
    }
}
