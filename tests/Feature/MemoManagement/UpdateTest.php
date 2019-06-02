<?php

namespace Test\Feature\MemoManagement;

use App\Models\Communications\Memo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group feature
 * @covers \App\Http\Controllers\Communication\MemoManagementController::update
 */
class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->put(route('memos.staff.update'), []);

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
        $response = $this->signIn($user)->put(route('memos.staff.update'), []);

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
            'status' => 'draft',
        ]);
        $memo->setRecipients([
            $user->uuid,
            $other->uuid,
        ]);

        $data = [
            'subject'    => $this->faker->sentence(5),
            'body'       => $this->faker->text,
            'status'     => 'sent',
            'recipients' => [
                $user->uuid,
                $other->uuid,
            ],
        ];

        // When
        $response = $this->signIn($staff)->put(route('memos.staff.update'), $data);

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_when_a_staff_member_updates_a_sent_memo()
    {
        // Given
        /** @var \App\Models\User $staff */
        $staff = $this->staff();

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $other */
        $other = $this->user();

        /** @var \App\Models\Communications\Memo $memo */
        $memo = factory(Memo::class)->make([
            'status' => 'sent',
        ]);
        $memo->setRecipients([
            $user->uuid,
            $other->uuid,
        ]);

        $data = [
            'subject'    => $this->faker->sentence(5),
            'body'       => $this->faker->text,
            'status'     => 'sent',
            'recipients' => [
                $user->uuid,
                $other->uuid,
            ],
        ];

        // When
        $response = $this->signIn($staff)->put(route('memos.staff.update'), $data);

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_a_staff_member_updates_a_draft_memo()
    {
        // Given
        /** @var \App\Models\User $staff */
        $staff = $this->staff();

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $other */
        $other = $this->user();

        /** @var \App\Models\User $newUser */
        $newUser = $this->user();

        /** @var \App\Models\Communications\Memo $memo */
        $memo = factory(Memo::class)->make([
            'status' => 'draft',
        ]);
        $memo->setRecipients([
            $user->uuid,
            $other->uuid,
        ]);

        $data = [
            'subject'    => $this->faker->words(5),
            'recipients' => [
                $user->uuid,
                $newUser->uuid,
            ],
        ];

        // When
        $response = $this->signIn($staff)->put(route('memos.staff.update'), $data);

        // Then
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('memos', [
            'subject' => $data['subject'],
        ]);
        $this->assertDatabaseHas('memo_user', [
            'user_id' => $user->id,
            'memo_id' => $memo->id,
        ]);
        $this->assertDatabaseMissing('memo_user', [
            'user_id' => $other->id,
            'memo_id' => $memo->id,
        ]);
        $this->assertDatabaseHas('memo_user', [
            'user_id' => $newUser->id,
            'memo_id' => $memo->id,
        ]);
    }
}
