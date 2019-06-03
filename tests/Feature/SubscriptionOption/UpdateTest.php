<?php

namespace Tests\Feature\SubscriptionOption;

use App\Models\Subscriptions\SubscriptionOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group feature
 * @covers \App\Http\Controllers\Subscription\SubscriptionOptionController::update
 */
class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->put(route('subscriptions.options.update', [123]), []);

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_a_staff_member()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Subscriptions\SubscriptionOption $options */
        $options = factory(SubscriptionOption::class)->create();

        // When
        $response = $this->signIn($user)->put(route('subscriptions.options.update', [$options->uuid]), []);

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_is_not_a_senior_staff_member()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->staff(User::JUNIOR_STAFF_MEMBER);

        /** @var \App\Models\Subscriptions\SubscriptionOption $options */
        $options = factory(SubscriptionOption::class)->create();

        // When
        $response = $this->signIn($user)->put(route('subscriptions.options.update', [$options->uuid]), []);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_code_will_be_returned_when_creating_a_subscription_plan_option()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->staff(User::SENIOR_STAFF_MEMBER);

        $optionKey    = $this->faker->word;
        $newOptionKey = $this->faker->word;

        /** @var \App\Models\Subscriptions\SubscriptionOption $options */
        $options = factory(SubscriptionOption::class)->create([
            'option_key' => $optionKey,
        ]);

        $data = [
            'option_key' => $newOptionKey,
        ];

        // When
        $response = $this->signIn($user)->put(route('subscriptions.options.update', [$options->uuid]), $data);

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['option_key' => $newOptionKey]);
    }
}
