<?php

namespace App\Http\Controllers\User;

use App\Exceptions\UserActivationTokenExpired;
use App\Exceptions\UserAlreadyActivated;
use App\Exceptions\UserNotActivated;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Account\AccountActivationRequest;
use App\Http\Requests\Account\AccountCancellationRequest;
use App\Http\Requests\Account\AccountDeactivationRequest;
use App\Http\Requests\Account\AccountRegistrationRequest;
use App\Http\Requests\Account\ResendAccountActivationRequest;
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
     * Registers user.
     *
     * @param \App\Http\Requests\Account\AccountRegistrationRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(AccountRegistrationRequest $request)
    {
        try {
            $this->userRepository->create($request->validated());

            return $this->responseCreated(trans('Successfully created user'));
        } catch (Exception $exception) {
            return $this->responseInternalError($exception->getMessage());
        }
    }

    /**
     * Activates pending user.
     *
     * @param \App\Http\Requests\Account\AccountActivationRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate(AccountActivationRequest $request)
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
     * @param \App\Http\Requests\Account\ResendAccountActivationRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendActivation(ResendAccountActivationRequest $request)
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
     * @param \App\Http\Requests\Account\AccountDeactivationRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate(AccountDeactivationRequest $request)
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
     * @param \App\Http\Requests\Account\AccountCancellationRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(AccountCancellationRequest $request)
    {
        try {
            $this->userRepository->delete($request->user);

            return $this->responseNoContent();
        } catch (Exception $runtimeException) {
            return $this->responseInternalError($runtimeException->getMessage());
        }
    }
}
