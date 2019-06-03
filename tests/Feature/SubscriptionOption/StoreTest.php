<?php

namespace Tests\Feature\SubscriptionOption;

use App\Models\Subscriptions\SubscriptionOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group feature
 * @coversNothing
 */
class StoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->post(route('subscriptions.options.store'), []);

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
        $response = $this->signIn($user)->post(route('subscriptions.options.store'), []);

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_is_not_a_senior_staff_member()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->staff(User::JUNIOR_STAFF_MEMBER);

        // When
        $response = $this->signIn($user)->post(route('subscriptions.options.store'), []);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_code_will_be_returned_when_creating_a_subscription_plan_option()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->staff(User::SENIOR_STAFF_MEMBER);

        $optionKey = $this->faker->word;

        /** @var \App\Models\Subscriptions\SubscriptionOption $option */
        $option = factory(SubscriptionOption::class)->make([
            'option_key' => $optionKey,
        ]);

        // When
        $response = $this->signIn($user)->post(route('subscriptions.options.store'), $option->toArray());

        // Then
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['option_key' => $option->option_key]);
    }
}
