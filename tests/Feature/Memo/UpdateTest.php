<?php

namespace Tests\Feature\MemoRecipient;

use App\Models\Communications\Memo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group feature
 * @covers \App\Http\Controllers\Communication\MemoRecipientController::update
 */
class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->put(route('memos.recipient.update', [123]), []);

        // Then
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_when_updating_a_memo_message_for_non_recipient()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $other */
        $other = $this->user();

        /** @var \App\Models\Communications\Memo $memo1 */
        $memo1 = factory(Memo::class)->create();
        $memo1->setRecipients([$user->uuid]);

        // When
        $response = $this->signIn($other)->put(route('memos.recipient.update', [$memo1->uuid]), [
            'is_read' => false,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_updating_a_memo_message_for_recipient()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Communications\Memo $memo1 */
        $memo1 = factory(Memo::class)->create();
        $memo1->setRecipients([$user->uuid]);
        $memo1->setIsRead($user, true);

        // When
        $response = $this->signIn($user)->put(route('memos.recipient.update', [$memo1->uuid]), [
            'is_read' => false,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('memo_user', [
            'user_id' => $user->id,
            'memo_id' => $memo1->id,
            'is_read' => false,
        ]);
    }
}
