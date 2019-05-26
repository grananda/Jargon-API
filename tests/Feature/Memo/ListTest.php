<?php

namespace Test\Feature\Memo;

use App\Models\Communications\Memo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group feature
 * @covers \App\Http\Controllers\Communication\MemoController::index
 */
class ListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->get(route('memos.index'));

        // Then
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_200_will_be_returned_when_listing_all_memos_for_a_recipient()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $other */
        $other = $this->user();

        /** @var \App\Models\Communications\Memo $memo1 */
        $memo1 = factory(Memo::class)->create();
        $memo1->setRecipients([$user->uuid]);

        /** @var \App\Models\Communications\Memo $memo2 */
        $memo2 = factory(Memo::class)->create();
        $memo2->setRecipients([$other->uuid]);

        // When
        $response = $this->signIn($user)->get(route('memos.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(1, 'data');
    }
}
