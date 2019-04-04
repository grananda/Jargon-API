<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\LoginRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;

class AuthController extends ApiController
{
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
                return $this->responseUnauthorized(trans('Unauthorized'));
            }

            /** @var \App\Models\User $user */
            $user = $request->user();

            if (! $user->isActivated()) {
                return $this->responseForbidden(trans('User is not active'));
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

            return response()->json([
                'access_token' => $tokenResult->accessToken,
                'token_type'   => 'Bearer',
                'expires_at'   => Carbon::parse(
                    $tokenResult->token->expires_at)
                    ->toDateTimeString(),
            ]);
        } catch (Exception $runtimeException) {
            return $this->responseInternalError($runtimeException->getMessage());
        }
    }

    public function resetPassword()
    {
    }
}
