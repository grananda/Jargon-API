<?php

namespace Tests\Feature\SubscriptionPlanOption;


use App\Models\Subscriptions\SubscriptionOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->get(route('subscriptions.plans.options.index'));

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
        $response = $this->signIn($user)->get(route('subscriptions.plans.options.index'));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_200_code_will_be_returned_when_requesting_subscription_plan_options()
    {
        // Given
        $optionValue = $this->faker->word;

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlanOption = factory(SubscriptionOption::class)->create([
            'option_key' => $optionValue,
        ]);

        // When
        $response = $this->get(route('subscriptions.plans.options.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['option_key' => $subscriptionPlanOption->option_key]);
    }
}