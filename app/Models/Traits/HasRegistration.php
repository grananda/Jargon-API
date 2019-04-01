<?php

namespace App\Models\Traits;

use App\Exceptions\UserActivationTokenExpired;
use App\Exceptions\UserAlreadyActivated;
use App\Exceptions\UserNotActivated;
use Carbon\Carbon;
use Illuminate\Support\Str;

trait HasRegistration
{
    /**
     * Determines if the User is activated.
     *
     * @return bool
     */
    public function isActivated()
    {
        return (bool) $this->activated_at;
    }

    /**
     * @throws \App\Exceptions\UserActivationTokenExpired
     * @throws \App\Exceptions\UserAlreadyActivated
     */
    public function activate()
    {
        if ($this->isActivated()) {
            throw new UserAlreadyActivated($this);
        }

        if ($this->hasActivationTokenExpired()) {
            throw new UserActivationTokenExpired($this);
        }

        $this->activated_at = $this->freshTimestamp();

        $this->save();

        $this->fireModelEvent('activated');

        return $this->fresh();
    }

    /**
     * Deactivates the User.
     *
     * @throws \App\Exceptions\UserNotActivated
     *
     * @return \App\Models\User
     */
    public function deactivate()
    {
        if (! $this->isActivated()) {
            throw new UserNotActivated($this);
        }

        $this->activation_token = null;
        $this->activated_at     = null;

        $this->save();

        $this->fireModelEvent('deactivated');

        return $this->fresh();
    }

    /**
     * Generates a new Activation Token for the User.
     *
     * @throws \App\Exceptions\UserAlreadyActivated
     *
     * @return \App\Models\User
     */
    public function generateActivationToken()
    {
        if ($this->isActivated()) {
            throw new UserAlreadyActivated($this);
        }

        $this->activation_token = Str::random(self::ACTIVATION_TOKEN_LENGTH);
        $this->activated_at     = null;

        $this->save();

        $this->fireModelEvent('activation-token-generated');

        return $this->fresh();
    }

    /**
     * Determines if the Activation Token has expired.
     *
     * @return bool
     */
    private function hasActivationTokenExpired()
    {
        return (bool) $this->created_at->lt(
            $this->getActivationExpirationTime()
        );
    }

    /**
     * Returns the expiration date.
     *
     * @return \Carbon\Carbon
     */
    private function getActivationExpirationTime()
    {
        return Carbon::now()->subHours(self::ACTIVATION_EXPIRES_AT);
    }
}
