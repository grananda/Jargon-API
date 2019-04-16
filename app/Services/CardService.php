<?php

namespace App\Services;

use App\Models\Card;
use App\Models\User;
use App\Repositories\CardRepository;
use App\Repositories\Stripe\StripeCardRepository;

class CardService
{
    /**
     * The StripeCardRepository instance.
     *
     * @var \App\Repositories\Stripe\StripeCardRepository
     */
    private $stripeCardRepository;

    /**
     * The CardRepository instance.
     *
     * @var \App\Repositories\CardRepository
     */
    private $cardRepository;

    /**
     * CardService constructor.
     *
     * @param \App\Repositories\Stripe\StripeCardRepository $stripeCardRepository
     * @param \App\Repositories\CardRepository              $cardRepository
     */
    public function __construct(StripeCardRepository $stripeCardRepository, CardRepository $cardRepository)
    {
        $this->stripeCardRepository = $stripeCardRepository;
        $this->cardRepository       = $cardRepository;
    }

    /**
     * Creates a Stripe card.
     *
     * @param \App\Models\User $user
     * @param string           $cardToken
     *
     * @throws \App\Exceptions\StripeApiCallException
     * @throws \Throwable
     *
     * @return mixed
     */
    public function registerCard(User $user, string $cardToken)
    {
        if ((bool) $user->cards()->count()) {
            $this->deleteCard($user->cards()->first());
        }

        /** @var array $cc */
        $cc = $this->stripeCardRepository->create($user, $cardToken);

        /** @var array $attributes */
        $attributes = array_merge($cc, ['stripe_id' => $cc['id']]);

        return $this->cardRepository->createCreditCard($user, $attributes);
    }

    /**
     * Updates a stripe card.
     *
     * @param \App\Models\Card $card
     * @param array            $attributes
     *
     * @throws \App\Exceptions\StripeApiCallException
     * @throws \Throwable
     *
     * @return \App\Models\Card
     */
    public function updateCard(Card $card, array $attributes)
    {
        /** @var \App\Models\Card $card */
        $card = $this->cardRepository->update($card, $attributes);

        /* @var array $cc */
        $this->stripeCardRepository->update($card->user, $card);

        return $card;
    }

    /**
     * Deletes a stripe card.
     *
     * @param \App\Models\Card $card
     *
     * @throws \Throwable
     * @throws \App\Exceptions\StripeApiCallException
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function deleteCard(Card $card)
    {
        $this->stripeCardRepository->delete($card);

        return $this->cardRepository->delete($card);
    }
}
