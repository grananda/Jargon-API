<?php

namespace Tests\Feature\Card;

use App\Models\Card;
use App\Repositories\Stripe\StripeCardRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @coversNothing
 */
class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array
     */
    private $stripeDeleteResponse;

    public function setUp(): void
    {
        parent::setUp();

        $this->stripeDeleteResponse = $this->loadFixture('stripe/card.delete.success');

        $this->mock(StripeCardRepository::class, function ($mock) {
            /* @var \Mockery\Mock $mock */
            $mock->shouldReceive('delete')
                ->withAnyArgs()
                ->andReturn($this->stripeDeleteResponse)
            ;
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

        // When
        $response = $this->delete(route('cards.destroy', [$card->uuid]));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_is_returned_when_user_not_allowed_to_delete_card()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Card $card */
        $card = factory(Card::class)->create();

        // When
        $response = $this->signIn($user)->delete(route('cards.destroy', [$card->uuid]));

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_is_returned_when_deleting_a_card()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Card $card */
        $card = factory(Card::class)->create([
            'user_id' => $user->id,
        ]);

        // When
        $response = $this->signIn($user)->delete(route('cards.destroy', [$card->uuid]));

        // Assert
        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('cards', [
            'user_id' => $user->id,
            'uuid'    => $card->uuid,
        ]);
    }
}
