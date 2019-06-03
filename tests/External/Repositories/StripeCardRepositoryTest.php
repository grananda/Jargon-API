<?php

namespace Tests\External\Repositories;

use App\Models\Card;
use App\Repositories\Stripe\StripeCardRepository;
use App\Repositories\Stripe\StripeCustomerRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group external
 * @covers \App\Repositories\Stripe\StripeCardRepository
 */
class StripeCardRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function adds_a_stripe_card()
    {
        // Given
        $user = $this->user();

        $stripeToken = 'tok_visa';

        /** @var \App\Repositories\Stripe\StripeCustomerRepository $stripeCustomerRepository */
        $stripeCustomerRepository = resolve(StripeCustomerRepository::class);

        /** @var StripeCardRepository $stripeCardRepository */
        $stripeCardRepository = resolve(StripeCardRepository::class);

        /** @var array $customer */
        $customer = $stripeCustomerRepository->create($user);
        $user->update(['stripe_id' => $customer['id']]);
        $user->refresh();

        // When
        $response = $stripeCardRepository->create($user, $stripeToken);

        $stripeCustomerRepository->delete($user);

        //Then
        $this->assertSame($response['object'], 'card');
        $this->assertSame($response['customer'], $customer['id']);
    }

    /** @test */
    public function updates_and_deletes_a_stripe_card()
    {
        // Given
        $user = $this->user();

        $stripeToken = 'tok_visa';

        /** @var \App\Repositories\Stripe\StripeCustomerRepository $stripeCustomerRepository */
        $stripeCustomerRepository = resolve(StripeCustomerRepository::class);

        /** @var StripeCardRepository $stripeCardRepository */
        $stripeCardRepository = resolve(StripeCardRepository::class);

        /** @var array $customer */
        $customer = $stripeCustomerRepository->create($user);
        $user->update(['stripe_id' => $customer['id']]);
        $user->refresh();

        // When
        $responseCreate = $stripeCardRepository->create($user, $stripeToken);

        /** @var \App\Models\Card $card */
        $card = factory(Card::class)->make([
            'user_id'         => $user->id,
            'stripe_id'       => $responseCreate['id'],
            'address_country' => 'Spain',
        ]);

        $responseUpdate = $stripeCardRepository->update($card);

        $responseDelete = $stripeCardRepository->delete($card);

        $stripeCustomerRepository->delete($user);

        //Then
        $this->assertSame($responseCreate['object'], 'card');
        $this->assertSame($responseCreate['customer'], $customer['id']);
        $this->assertSame($responseUpdate['address_country'], 'Spain');
        $this->assertTrue($responseDelete);
    }
}
