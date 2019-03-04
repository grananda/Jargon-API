<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\LoginRequest;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;

class AuthController extends ApiController
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

    public function login(LoginRequest $request)
    {
        try {
            /** @var array $credentials */
            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials, $request->remember_me)) {
                return $this->responseUnauthorized(trans('Unauthorized'));
            }

            /** @var \App\Models\User $user */
            $user = $request->user();

            if (!$user->isActivated()) {
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

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return $this->responseOk(trans('Successfully logged out'));
    }
}
