<?php

namespace App\Http\Controllers\User;

use App\Exceptions\UserActivationTokenExpired;
use App\Exceptions\UserAlreadyActivated;
use App\Exceptions\UserNotActivated;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Auth\ResendUserActivationRequest;
use App\Http\Requests\Auth\UserActivationRequest;
use App\Http\Requests\Auth\UserCancellationRequest;
use App\Http\Requests\Auth\UserDeactivationRequest;
use App\Repositories\UserRepository;
use Exception;

class AccountController extends ApiController
{
    /**
     * The UserRepository instance.
     *
     * @var \App\Repositories\UserRepository
     */
    private $userRepository;

    /**
     * AuthController constructor.
     *
     * @param \App\Repositories\UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Activates pending user.
     *
     * @param \App\Http\Requests\Auth\UserActivationRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate(UserActivationRequest $request)
    {
        try {
            /** @var \App\Models\User $user */
            $user = $request->user->activate();

            return $this->responseOk($user->activated_at);
        } catch (UserActivationTokenExpired $userActivationTokenExpiredException) {
            return $this->responseInternalError($userActivationTokenExpiredException->getMessage());
        } catch (UserAlreadyActivated $userAlreadyActivatedException) {
            return $this->responseInternalError($userAlreadyActivatedException->getMessage());
        } catch (Exception $runtimeException) {
            return $this->responseInternalError($runtimeException->getMessage());
        }
    }

    /**
     * Recreates a new activation token and send email.
     *
     * @param \App\Http\Requests\Auth\ResendUserActivationRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendActivation(ResendUserActivationRequest $request)
    {
        try {
            $request->user->generateActivationToken();

            return $this->responseNoContent();
        } catch (UserAlreadyActivated $userAlreadyActivatedException) {
            return $this->responseInternalError($userAlreadyActivatedException->getMessage());
        } catch (Exception $runtimeException) {
            return $this->responseInternalError($runtimeException->getMessage());
        }
    }

    /**
     * Deactivates an active user.
     *
     * @param \App\Http\Requests\Auth\UserDeactivationRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate(UserDeactivationRequest $request)
    {
        try {
            $request->user->deactivate();

            return $this->responseNoContent();
        } catch (UserNotActivated $userNotActivatedException) {
            return $this->responseInternalError($userNotActivatedException->getMessage());
        } catch (Exception $runtimeException) {
            return $this->responseInternalError($runtimeException->getMessage());
        }
    }

    /**
     * Cancels a user account.
     *
     * @param \App\Http\Requests\Auth\UserCancellationRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(UserCancellationRequest $request)
    {
        try {
            $this->userRepository->delete($request->user);

            return $this->responseNoContent();
        } catch (Exception $runtimeException) {
            return $this->responseInternalError($runtimeException->getMessage());
        }
    }
}
