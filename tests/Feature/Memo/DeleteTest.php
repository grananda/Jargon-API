<?php

namespace Test\Feature\Memo;

use App\Models\Communications\Memo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group feature
 * @covers \App\Http\Controllers\Communication\MemoController::destroy
 */
class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->delete(route('memos.destroy', [123]));

        // Then
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_when_a_non_recipient_deletes_a_memo_message()
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
        $response = $this->signIn($other)->delete(route('memos.destroy', [$memo1->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_when_a_recipient_deletes_a_draft_memo_message()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Communications\Memo $memo1 */
        $memo1 = factory(Memo::class)->create(['status' => 'draft']);
        $memo1->setRecipients([$user->uuid]);

        // When
        $response = $this->signIn($user)->delete(route('memos.destroy', [$memo1->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_a_recipient_deletes_a_memo_message()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Communications\Memo $memo1 */
        $memo1 = factory(Memo::class)->create();
        $memo1->setRecipients([$user->id]);

        // When
        $response = $this->signIn($user)->delete(route('memos.destroy', [$memo1->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('memo_user', [
            'user_id' => $user->id,
            'memo_id' => $memo1->id,
        ]);
    }
}
