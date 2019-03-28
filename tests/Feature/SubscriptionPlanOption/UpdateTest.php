<?php

namespace Tests\Feature\SubscriptionPlanOption;


use App\Events\SubscriptionPlanOptionWasUpdated;
use App\Models\Subscriptions\SubscriptionOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->put(route('subscriptions.plans.options.update'), []);

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_a_staff_member()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        // When
        $response = $this->signIn($user)->put(route('subscriptions.plans.options.update'), []);

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
        $response = $this->signIn($user)->put(route('subscriptions.plans.options.update'), []);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_code_will_be_returned_when_creating_a_subscription_plan_option()
    {
        // Given
        Event::faker();

        /** @var \App\Models\User $user */
        $user = $this->staff(User::SENIOR_STAFF_MEMBER);

        $optionKey = $this->faker->word;
        $optionValue = 5;
        $newOptionValue = 10;

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlanOption = factory(SubscriptionOption::class)->create([
            'option_key'           => $optionKey,
            'option_value'         => $optionValue,
            'update_subscriptions' => false,
        ]);

        $data = [
            'option_value' => $newOptionValue,
        ];

        // When
        $response = $this->signIn($user)->put(route('subscriptions.plans.options.update'), $data);

        // Then
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['option_key' => $subscriptionPlanOption->option_key]);

        Event::assertDispatched(SubscriptionPlanOptionWasUpdated::class);
    }
}