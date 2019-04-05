<?php

namespace App\Services;

use App\Exceptions\UnauthorizedUserException;
use App\Exceptions\UserNotActivated;
use App\Models\User;
use App\Repositories\PasswordResetRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;

class AuthService
{
    /**
     * @var \App\Repositories\UserRepository
     */
    private $userRepository;

    /**
     * @var \App\Repositories\PasswordResetRepository
     */
    private $passwordResetRepository;

    /**
     * AuthService constructor.
     *
     * @param \App\Repositories\UserRepository          $userRepository
     * @param \App\Repositories\PasswordResetRepository $passwordResetRepository
     */
    public function __construct(UserRepository $userRepository, PasswordResetRepository $passwordResetRepository)
    {
        $this->userRepository          = $userRepository;
        $this->passwordResetRepository = $passwordResetRepository;
    }

    /**
     * @param array $credentials
     * @param bool  $rememberMe
     *
     * @throws \App\Exceptions\UnauthorizedUserException
     * @throws \App\Exceptions\UserNotActivated
     *
     * @return \Laravel\Passport\PersonalAccessTokenResult
     */
    public function userLogin(array $credentials, bool $rememberMe)
    {
        if (! Auth::attempt($credentials, $rememberMe)) {
            throw new UnauthorizedUserException(trans('Unauthorized'));
        }

        if (! $user->isActivated()) {
            throw new UserNotActivated(trans('User is not active'));
        }

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

        return $tokenResult;
    }

    /**
     * Generates a password recovery token.
     *
     * @param array $credentials
     *
     * @throws \Throwable
     *
     * @return string
     */
    public function resetPassword(array $credentials)
    {
        $response = $this->broker()->reset(
            $credentials, function ($user, $password) {
                $this->writePassword($user, $password);
            });

        $this->clearPasswordReserRequest($credentials['email']);

        return $response;
    }

    /**
     * Reset the given user's password.
     *
     * @param \Illuminate\Contracts\Auth\CanResetPassword $user
     * @param string                                      $password
     *
     * @throws \Throwable
     *
     * @return void
     */
    private function writePassword($user, $password)
    {
        $this->userRepository->update($user, [
            'password'       => Hash::make($password),
            'remember_token' => Str::random(60),
        ]);

        return $this->userLogin($user);
    }

    /**
     * Removes current user password reset request.
     *
     * @param string $email
     *
     * @throws \Throwable
     */
    private function clearPasswordReserRequest(string $email)
    {
        /** @var \App\Models\PasswordReset $passwordReset */
        $passwordReset = $this->passwordResetRepository->findBy(['email' => $email]);

        $this->passwordResetRepository->delete($passwordReset);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    private function broker()
    {
        return Password::broker();
    }
}
