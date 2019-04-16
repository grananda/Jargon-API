<?php


namespace Tests\Unit\Services;


use App\Models\Card;
use App\Repositories\CardRepository;
use App\Repositories\Stripe\StripeCardRepository;
use App\Services\CardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function adds_a_valid_card_for_customer()
    {
        // Given
        $cardToken = 'tok_visa';

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var array $response */
        $response = $this->loadFixture('stripe/card.create.success');

        /** @var \App\Repositories\CardRepository $cardRepository */
        $cardRepository = resolve(CardRepository::class);

        $stripeCardRepository = $this->createMock(StripeCardRepository::class);
        $stripeCardRepository->method('create')
            ->willReturn($response);

        /** @var CardService $cardService */
        $cardService = new CardService($stripeCardRepository, $cardRepository);

        // When
        $card = $cardService->registerCard($user, $cardToken);

        // Then
        $this->assertEquals($card->stripe_id, $response['id']);
    }

    /** @test */
    public function replaces_a_valid_card_for_customer()
    {
        // Given
        $cardToken = 'tok_visa';

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var Card $card */
        $card = factory(Card::class)->create([
            'user_id' => $user->id,
        ]);

        /** @var array $responseCreated */
        $responseCreated = $this->loadFixture('stripe/card.create.success');

        /** @var array $responseCreated */
        $responseDeleted = $this->loadFixture('stripe/card.delete.success');

        /** @var \App\Repositories\CardRepository $cardRepository */
        $cardRepository = resolve(CardRepository::class);

        $stripeCardRepository = $this->createMock(StripeCardRepository::class);

        $stripeCardRepository->method('create')
            ->willReturn($responseCreated);
        $stripeCardRepository->method('delete')
            ->willReturn($responseDeleted);


        /** @var CardService $cardService */
        $cardService = new CardService($stripeCardRepository, $cardRepository);

        // When
        $replacedCard = $cardService->registerCard($user, $cardToken);

        // Then
        $this->assertEquals($replacedCard->stripe_id, $responseCreated['id']);
        $this->assertDatabaseMissing('cards', [
            'stripe_id'=>$card->stripe_id,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('cards', [
            'stripe_id'=>$replacedCard->stripe_id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function updated_a_valid_existing_card_information()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var array $response */
        $response = $this->loadFixture('stripe/card.update.success');

        /** @var \App\Repositories\CardRepository $cardRepository */
        $cardRepository = resolve(CardRepository::class);

        $stripeCardRepository = $this->createMock(StripeCardRepository::class);
        $stripeCardRepository->method('update')
            ->willReturn($response);

        /** @var CardService $cardService */
        $cardService = new CardService($stripeCardRepository, $cardRepository);

        /** @var Card $card */
        $card = factory(Card::class)->create([
            'user_id' => $user->id,
        ]);

        $attributes = [
            'address_city'    => $this->faker->city,
            'address_country' => $this->faker->country,
        ];

        // When
        $card = $cardService->updateCard($card, $attributes);

        // Then
        $this->assertEquals($card->address_city, $attributes['address_city']);
        $this->assertEquals($card->address_country, $attributes['address_country']);
    }

    /** @test */
    public function deletes_a_valid_card_for_customer()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var array $response */
        $response = $this->loadFixture('stripe/card.delete.success');

        /** @var \App\Repositories\CardRepository $cardRepository */
        $cardRepository = resolve(CardRepository::class);

        $stripeCardRepository = $this->createMock(StripeCardRepository::class);
        $stripeCardRepository->method('delete')
            ->willReturn($response);

        /** @var CardService $cardService */
        $cardService = new CardService($stripeCardRepository, $cardRepository);

        /** @var Card $card */
        $card = factory(Card::class)->create([
            'user_id' => $user->id,
        ]);

        // When
        $card = $cardService->deleteCard($card);

        // Then
        $this->assertDatabaseMissing('cards', [
            'user_id' => $user->id,
        ]);
    }
}