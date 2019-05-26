<?php

namespace Test\Feature\MemoManagement;

use App\Models\Communications\Memo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group feature
 * @covers \App\Http\Controllers\Communication\MemoManagementController::store
 */
class ListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->post(route('memos.staff.store'), []);

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
        $response = $this->signIn($user)->post(route('memos.staff.store'), []);

        // Then
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_when_a_junior_staff_member_creates_a_memo()
    {
        // Given
        /** @var \App\Models\User $staff */
        $staff = $this->staff('junior-staff');

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $other */
        $other = $this->user();

        /** @var \App\Models\Communications\Memo $memo */
        $memo = factory(Memo::class)->make([
            'status' => 'sent',
        ]);

        $data = array_merge($memo->toArray(), [
            'recipients' => [
                $user->uuid,
                $other->uuid,
            ],
        ]);

        // When
        $response = $this->signIn($staff)->post(route('memos.staff.store'), $data);

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_a_staff_member_creates_a_memo()
    {
        // Given
        /** @var \App\Models\User $staff */
        $staff = $this->staff();

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $other */
        $other = $this->user();

        /** @var array$memo */
        $memo = factory(Memo::class)->make([
            'status' => 'sent',
        ])->toArray();

        $data = array_merge($memo, [
            'recipients' => [
                $user->uuid,
                $other->uuid,
            ],
        ]);

        // When
        $response = $this->signIn($staff)->post(route('memos.staff.store'), $data);

        // Then
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('memos', [
            'subject' => $memo['subject'],
        ]);
        $this->assertDatabaseHas('memo_user', [
            'user_id' => $user->id,
            'memo_id' => $memo->id,
        ]);
        $this->assertDatabaseHas('memo_user', [
            'user_id' => $other->id,
            'memo_id' => $memo->id,
        ]);
    }
}
