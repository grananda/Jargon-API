<?php

namespace Test\Feature\Memo;

use App\Models\Communications\Memo;
use App\Models\Organization;
use App\Models\Translations\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group feature
 * @coversNothing
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
    public function a_200_will_be_returned_when_listing_all_memos_for_a_user()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        $memo = factory(Memo::class, 10)->create();

        // When
        $response = $this->signIn($user)->get(route('memos.index'));

        // Then
        $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }
}
