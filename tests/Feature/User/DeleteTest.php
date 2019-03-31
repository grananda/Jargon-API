<?php


namespace Tests\Feature\User;


use App\Events\User\UserWasDeleted;
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

        // When
        $response = $this->delete(route('users.destroy', [$user->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('users', [
            'uuid'  => $user->uuid,
        ]);

        Event::assertDispatched(UserWasDeleted::class);
    }
}