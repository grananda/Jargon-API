<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\User\DeleteUserRequest;
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

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\User\DeleteUserRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteUserRequest $request)
    {
        try {
            $this->userRepository->delete($request->user);

            return $this->responseNoContent();
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
