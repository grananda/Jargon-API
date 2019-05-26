<?php

namespace Test\Feature\Memo;

use App\Models\Communications\Memo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group feature
 * @covers \App\Http\Controllers\Communication\MemoManagementController::destroy
 */
class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->delete(route('memos.staff.destroy', [123]));

        // Then
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_a_staff()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        // When
        $response = $this->signIn($user)->delete(route('memos.staff.destroy', [123]));

        // Then
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function a_403_will_be_returned_when_a_junior_staff_member_deletes_a_memo_message()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $staff */
        $staff = $this->staff();

        /** @var \App\Models\Communications\Memo $memo1 */
        $memo1 = factory(Memo::class)->create();
        $memo1->setRecipients([$user->uuid]);

        // When
        $response = $this->signIn($staff)->delete(route('memos.staff.delete', [$memo1->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_a_recipient_deletes_a_memo_message()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $staff */
        $staff = $this->staff();

        /** @var \App\Models\Communications\Memo $memo1 */
        $memo1 = factory(Memo::class)->create();
        $memo1->setRecipients([$user->uuid]);

        // When
        $response = $this->signIn($staff)->delete(route('memos.staff.delete', [$memo1->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('memo_user', [
            'user_id' => $user->id,
            'memo_id' => $memo1->id,
        ]);
        $this->assertDatabaseMissing('memos', [
            'uuid' => $memo1->uuid,
        ]);
    }
}
