<?php

namespace Test\Feature\MemoManagement;

use App\Models\Communications\Memo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group feature
 * @covers \App\Http\Controllers\Communication\MemoManagementController::index
 */
class ListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->get(route('memos.staff.index'));

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
        $response = $this->signIn($user)->get(route('memos.staff.index'));

        // Then
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_when_listing_all_memos_for_a_junior_staff_member()
    {
        // Given
        /** @var \App\Models\User $staff */
        $staff = $this->staff('junior-staff');

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
        $response = $this->signIn($staff)->get(route('memos.staff.index'));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_listing_all_memos_for_a_staff_member()
    {
        // Given
        /** @var \App\Models\User $staff */
        $staff = $this->staff();

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
        $response = $this->signIn($staff)->get(route('memos.staff.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(2, 'data');
    }
}
