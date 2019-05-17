<?php

namespace Tests\Feature\Card;

use App\Exceptions\StripeApiCallException;
use App\Models\Card;
use App\Repositories\Stripe\StripeCardRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @coversNothing
 */
class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array
     */
    private $stripeCreateResponse;

    /**
     * @var array
     */
    private $stripeDeleteResponse;

    public function setUp(): void
    {
        parent::setUp();

        $this->stripeCreateResponse = $this->loadFixture('stripe/card.create.success');

        $this->stripeDeleteResponse = $this->loadFixture('stripe/card.delete.success');

        $this->mock(StripeCardRepository::class, function ($mock) {
            /* @var \Mockery\Mock $mock */
            $mock->shouldReceive('create')
                ->withAnyArgs()
                ->andReturn($this->stripeCreateResponse)
            ;

            $mock->shouldReceive('delete')
                ->withAnyArgs()
                ->andReturn($this->stripeDeleteResponse)
            ;
        });
    }

    /** @test */
    public function a_401_is_returned_when_not_logged_in()
    {
        // When
        $response = $this->post(route('cards.store'));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_is_returned_when_user_is_not_a_stripe_customer()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user('registered-user', ['stripe_id' => null]);

        $data = [
            'stripeCardToken' => 'tok_visa',
        ];

        // When
        $response = $this->signIn($user)->post(route('cards.store'), $data);

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_is_returned_when_invalid_stripe_card_token()
    {
        // Given
        $this->mock(StripeCardRepository::class, function ($mock) {
            /* @var \Mockery\Mock $mock */
            $mock->shouldReceive('create')
                ->withAnyArgs()
                ->andThrowExceptions([new StripeApiCallException()])
            ;
        });

        /** @var \App\Models\User $user */
        $user = $this->user();

        $data = [
            'stripeCardToken' => 'tok_chargeDeclinedProcessingError',
        ];

        // When
        $response = $this->signIn($user)->post(route('cards.store'), $data);

        // Then
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /** @test */
    public function a_200_is_returned_when_a_card_is_created()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        $data = [
            'stripeCardToken' => 'tok_visa',
        ];

        // When
        $response = $this->signIn($user)->post(route('cards.store'), $data);

        // Then
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertSame(1, $user->fresh()->cards()->count());
        $this->assertDatabaseHas('cards', [
            'user_id'   => $user->id,
            'stripe_id' => $this->stripeCreateResponse['id'],
        ]);
    }

    /** @test */
    public function a_200_is_returned_when_a_second_card_is_created()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        factory(Card::class)->create([
            'user_id' => $user->id,
        ]);

        $data = [
            'stripeCardToken' => 'tok_visa',
        ];

        // When
        $response = $this->signIn($user)->post(route('cards.store'), $data);

        // Then
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertSame(1, $user->fresh()->cards()->count());
        $this->assertDatabaseHas('cards', [
            'user_id'   => $user->id,
            'stripe_id' => $this->stripeCreateResponse['id'],
            'brand'     => $this->stripeCreateResponse['brand'],
        ]);
    }
}
