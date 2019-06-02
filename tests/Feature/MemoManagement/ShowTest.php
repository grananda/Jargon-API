<?php

namespace Tests\Feature\MemoManagement;

use App\Models\Communications\Memo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group feature
 * @covers \App\Http\Controllers\Communication\MemoController::show
 */
class ShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->get(route('memos.staff.show', [123]));

        // Then
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function a_401_will_be_returned_if_the_user_is_not_a_staff()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        // When
        $response = $this->get(route('memos.staff.show', [123]));

        // Then
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_when_displaying_a_memo_message_for_junior_staff()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $other */
        $staff = $this->user('junior-staff');

        /** @var \App\Models\Communications\Memo $memo1 */
        $memo1 = factory(Memo::class)->create();
        $memo1->setRecipients([$user->uuid]);

        // When
        $response = $this->signIn($staff)->get(route('memos.staff.show', [$memo1->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas('memo_user', [
            'user_id' => $user->id,
            'memo_id' => $memo1->id,
            'is_read' => false,
        ]);
    }

    /** @test */
    public function a_200_will_be_returned_when_displaying_a_memo_message_for_staff()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $other */
        $staff = $this->staff();

        /** @var \App\Models\Communications\Memo $memo1 */
        $memo1 = factory(Memo::class)->create();
        $memo1->setRecipients([$user->uuid]);

        // When
        $response = $this->signIn($staff)->get(route('memos.staff.show', [$memo1->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('memo_user', [
            'user_id' => $user->id,
            'memo_id' => $memo1->id,
            'is_read' => false,
        ]);
    }
}
