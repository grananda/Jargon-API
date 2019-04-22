<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\User;

class CardPolicy extends AbstractPolicy
{
    /**
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function list(User $user)
    {
        return true;
    }

    /**
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function create(User $user)
    {
        return $user->isStripeCustomer();
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Card $card
     *
     * @return bool
     */
    public function update(User $user, Card $card)
    {
        return $user->isStripeCustomer() && $user->uuid === $card->user->uuid;
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Card $card
     *
     * @return bool
     */
    public function delete(User $user, Card $card)
    {
        return $user->isStripeCustomer() && $user->uuid === $card->user->uuid;
    }
}
