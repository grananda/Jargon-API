<?php

namespace Tests\Feature\Option;

use App\Models\Options\Option;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group feature
 * @covers \App\Http\Controllers\Option\OptionController::index
 */
class ListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->get(route('options.index'));

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
        $response = $this->signIn($user)->get(route('options.index'));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_200_code_will_be_returned_when_requesting_subscription_plan_options()
    {
        // Given
        $user = $this->staff(User::JUNIOR_STAFF_MEMBER);

        $optionValue = $this->faker->word;

        /** @var \App\Models\Options\Option $option */
        $option = factory(Option::class)->create([
            'option_key' => $optionValue,
        ]);

        // When
        $response = $this->signIn($user)->get(route('options.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['option_key' => $option->option_key]);
    }
}
