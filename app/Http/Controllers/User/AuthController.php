<?php

namespace App\Http\Controllers\User;

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
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;

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

            if (! Auth::attempt($credentials, $request->remember_me)) {
                throw new UnauthorizedUserException(trans('Unauthorized'));
            }

            /** @var \App\Models\User $user */
            $user = $request->user();

//            $tokenResult = $this->authService->userLogin($credentials $request->remember_me);
//
            if (! $user->isActivated()) {
                throw new UserNotActivated(trans('User is not active'));
            }

            // Force the user password to be rehashed, only if it's required
            if (password_needs_rehash($user->password, PASSWORD_DEFAULT)) {
                $user->update(compact('password'));
            }

            /** @var \Laravel\Passport\PersonalAccessTokenResult $tokenResult */
            $tokenResult = $user->createToken('Personal Access Token');

            /** @var \Laravel\Passport\Token $token */
            $token = $tokenResult->token;

            if ($request->remember_me) {
                $token->expires_at = Carbon::now()->add(Passport::tokensExpireIn());
            }

            $token->save();

            return $this->responseOk([
                'access_token' => $tokenResult->accessToken,
                'token_type'   => 'Bearer',
                'expires_at'   => Carbon::parse(
                    $tokenResult->token->expires_at),
            ]);
        } catch (UnauthorizedUserException $unauthorizedUserException) {
            return $this->responseInternalError($unauthorizedUserException->getMessage());
        } catch (UserNotActivated $userNotActivated) {
            return $this->responseInternalError($userNotActivated->getMessage());
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
    public function PasswordReset(AccountPasswordRequestRequest $request)
    {
        try {
            $this->authService->resetPassword($request->validated());

            return $this->responseNoContent();
        } catch (Exception $runtimeException) {
            return $this->responseInternalError($runtimeException->getMessage());
        }
    }
}
