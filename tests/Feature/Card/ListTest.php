<?php


namespace Tests\Feature\Card;


use App\Models\Card;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_is_returned_when_not_logged_in()
    {
        // When
        $response = $this->post(route('cards.index'));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_200_is_returned_when_requesting_a_card()
    {
        // Given
        /** @var \App\Models\User $user1 */
        $user1 = $this->user();

        /** @var \App\Models\Card $card1 */
        $card1 = factory(Card::class)->create([
            'user_id' => $user1->id,
        ]);

        /** @var \App\Models\User $user2 */
        $user2 = $this->user();

        /** @var \App\Models\Card $card2 */
        $card2 = factory(Card::class)->create([
            'user_id' => $user2->id,
        ]);

        // When
        $response = $this->signIn($user1)->get(route('cards.index'));

        // Assert
        $response->assertStatus(Response::HTTP_OK);
        $response->assertDontSee($card1->uuid);
        $response->assertDontSeeText($card2->uuid);

    }
}