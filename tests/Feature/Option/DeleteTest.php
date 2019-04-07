<?php

namespace Tests\Feature\Option;


use App\Events\Option\OptionWasDeleted;
use App\Models\Options\Option;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->delete(route('options.destroy', [123]));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_a_staff_member()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        // When
        $response = $this->signIn($user)->delete(route('options.destroy', [123]));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_is_not_a_senior_staff_member()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->staff(User::JUNIOR_STAFF_MEMBER);

        /** @var Option $option */
        $option = factory(Option::class)->create();

        // When
        $response = $this->signIn($user)->delete(route('options.destroy', $option->uuid));

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_code_will_be_returned_when_deleting_a_subscription_plan_option()
    {
        // Given
        Event::fake(OptionWasDeleted::class);

        /** @var \App\Models\User $user */
        $user = $this->staff(User::SENIOR_STAFF_MEMBER);

        /** @var Option $option */
        $option = factory(Option::class)->create();

        // When
        $response = $this->signIn($user)->delete(route('options.destroy', $option->uuid));

        // Then
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('options', [
            'uuid' => $option->uuid,
        ]);

        Event::assertDispatched(OptionWasDeleted::class);
    }
}