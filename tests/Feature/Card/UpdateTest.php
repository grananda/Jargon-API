<?php


namespace Tests\Feature\Card;


use App\Models\Card;
use App\Repositories\Stripe\StripeCardRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array
     */
    private $stripeUpdateResponse;

    public function setUp(): void
    {
        parent::setUp();

        $this->stripeUpdateResponse = $this->loadFixture('stripe/card.update.success');

        $this->mock(StripeCardRepository::class, function ($mock) {
            /** @var \Mockery\Mock $mock */
            $mock->shouldReceive('update')
                ->withAnyArgs()
                ->andReturn($this->stripeUpdateResponse);
        });
    }

    /** @test */
    public function a_401_is_returned_when_not_logged_in()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Card $card */
        $card = factory(Card::class)->create([
            'user_id' => $user->id,
        ]);

        $data = [];

        // When
        $response = $this->put(route('cards.update', [$card->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_is_returned_when_user_not_allowed_to_update_card()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Card $card */
        $card = factory(Card::class)->create();

        $data = [];

        // When
        $response = $this->signIn($user)->put(route('cards.update', [$card->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_is_returned_when_user_updates_a_card()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Card $card */
        $card = factory(Card::class)->create([
            'user_id' => $user->id,
        ]);

        $data = [
            'address_city' => $this->faker->city,
        ];

        // When
        $response = $this->signIn($user)->put(route('cards.update', [$card->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_OK);
        $response->assertSeeText($data['address_city']);
    }
}