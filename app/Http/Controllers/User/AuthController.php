<?php

namespace App\Http\Controllers\User;

use App\Exceptions\PasswordResetFailException;
use App\Exceptions\UnauthorizedUserException;
use App\Exceptions\UserNotActivated;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Account\AccountPasswordRequestRequest;
use App\Http\Requests\Account\AccountRequestPasswordResetRequest;
use App\Http\Requests\LoginRequest;
use App\Repositories\PasswordResetRepository;
use App\Services\AuthService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Auth\PasswordBroker;

class AuthController extends ApiController
{
    /**
     * @var \App\Services\AuthService
     */
    private $authService;

    /**
     * @var \App\Repositories\PasswordResetRepository
     */
    private $passwordResetRepository;

    /**
     * AuthController constructor.
     *
     * @param \App\Repositories\PasswordResetRepository $passwordResetRepository
     * @param \App\Services\AuthService                 $authService
     */
    public function __construct(PasswordResetRepository $passwordResetRepository, AuthService $authService)
    {
        $this->passwordResetRepository = $passwordResetRepository;

        $this->authService = $authService;
    }

    /**
     * Login user and returns credential token.
     *
     * @param \App\Http\Requests\LoginRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        try {
            /** @var array $credentials */
            $credentials = request(['email', 'password']);

            $rememberMe = $request->remember_me ?? false;

            $tokenResult = $this->authService->userLogin($credentials, $rememberMe);

            return $this->responseOk([
                'access_token' => $tokenResult->accessToken,
                'token_type'   => 'Bearer',
                'expires_at'   => Carbon::parse(
                    $tokenResult->token->expires_at),
            ]);
        } catch (UnauthorizedUserException $unauthorizedUserException) {
            return $this->responseUnauthorized($unauthorizedUserException->getMessage());
        } catch (UserNotActivated $userNotActivated) {
            return $this->responseUnauthorized($userNotActivated->getMessage());
        } catch (Exception $runtimeException) {
            return $this->responseInternalError($runtimeException->getMessage());
        }
    }

    /**
     * Requests a password recovery link.
     *
     * @param \App\Http\Requests\Account\AccountRequestPasswordResetRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestPasswordReset(AccountRequestPasswordResetRequest $request)
    {
        try {
            $this->passwordResetRepository->createPasswordReset($request->user);

            return $this->responseNoContent();
        } catch (Exception $runtimeException) {
            return $this->responseInternalError($runtimeException->getMessage());
        }
    }

    /**
     * Securely resets password.
     *
     * @param \App\Http\Requests\Account\AccountPasswordRequestRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function passwordReset(AccountPasswordRequestRequest $request)
    {
        try {
            $response = $this->authService->resetPassword($request->credentials());

            if ($response !== PasswordBroker::PASSWORD_RESET) {
                throw new PasswordResetFailException(trans('The password reset process has failed.'));
            }

            $credentials = request(['email', 'password']);

            $tokenResult = $this->authService->userLogin($credentials);

            return $this->responseOk([
                'access_token' => $tokenResult->accessToken,
                'token_type'   => 'Bearer',
                'expires_at'   => Carbon::parse(
                    $tokenResult->token->expires_at),
            ]);
        } catch (PasswordResetFailException $passwordResetFailException) {
            return $this->responseInternalError($passwordResetFailException->getMessage());
        } catch (Exception $runtimeException) {
            return $this->responseInternalError($runtimeException->getMessage());
        }
    }
}
