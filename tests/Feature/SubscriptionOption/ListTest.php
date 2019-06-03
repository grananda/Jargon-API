<?php

namespace Tests\Feature\SubscriptionOption;

use App\Models\Subscriptions\SubscriptionOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group feature
 * @covers \App\Http\Controllers\Subscription\SubscriptionOptionController::index
 */
class ListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->get(route('subscriptions.options.index'));

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
        $response = $this->signIn($user)->get(route('subscriptions.options.index'));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_200_code_will_be_returned_when_requesting_subscription_plan_options()
    {
        // Given
        /** @var User $staff */
        $staff = $this->staff(User::SENIOR_STAFF_MEMBER);

        $optionValue = $this->faker->word;

        /** @var \App\Models\Subscriptions\SubscriptionOption $option */
        $option = factory(SubscriptionOption::class)->create([
            'option_key' => $optionValue,
        ]);

        // When
        $response = $this->signIn($staff)->get(route('subscriptions.options.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['option_key' => $option->option_key]);
    }
}
