<?php

namespace Tests\Feature\SubscriptionOption;


use App\Events\SubscriptionOption\SubscriptionOptionWasDeleted;
use App\Models\Subscriptions\SubscriptionOption;
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
        // Given
        /** @var \App\Models\Subscriptions\SubscriptionOption $option */
        $option = factory(SubscriptionOption::class)->create();

        // When
        $response = $this->delete(route('subscriptions.options.destroy', [$option->uuid]));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_a_staff_member()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Subscriptions\SubscriptionOption $option */
        $option = factory(SubscriptionOption::class)->create();

        // When
        $response = $this->signIn($user)->delete(route('subscriptions.options.destroy', [$option->uuid]));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_is_not_a_senior_staff_member()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->staff(User::JUNIOR_STAFF_MEMBER);

        /** @var \App\Models\Subscriptions\SubscriptionOption $option */
        $option = factory(SubscriptionOption::class)->create();

        // When
        $response = $this->signIn($user)->delete(route('subscriptions.options.destroy', [$option->uuid]));

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_code_will_be_returned_when_deleting_a_subscription_plan_option()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->staff(User::SENIOR_STAFF_MEMBER);

        $optionKey = $this->faker->word;

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlanOption = factory(SubscriptionOption::class)->create([
            'option_key' => $optionKey,
        ]);

        // When
        $response = $this->signIn($user)->delete(route('subscriptions.options.destroy', $subscriptionPlanOption->uuid));

        // Then
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('options', [
            'uuid' => $subscriptionPlanOption->uuid,
        ]);
    }
}