<?php


namespace Tests\Feature\User;


use App\Events\User\UserWasDeleted;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_200_will_be_returned_when_a_user_is_created()
    {
        // Given
        Event::fake(UserWasDeleted::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $user */
        $staff = $this->staff(User::SENIOR_STAFF_MEMBER);

        // When
        $response = $this->signIn($staff)->delete(route('users.destroy', [$user->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('users', [
            'uuid'  => $user->uuid,
        ]);

        Event::assertDispatched(UserWasDeleted::class);
    }
}