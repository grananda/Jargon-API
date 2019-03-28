<?php

namespace Tests\Feature\SubscriptionPlan;


use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->delete(route('subscriptions.plans.destroy', [123]));

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
        $response = $this->signIn($user)->delete(route('subscriptions.plans.destroy', [123]));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_is_not_a_senior_staff_member()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->staff(User::JUNIOR_STAFF_MEMBER);

        /** @var array $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create();

        // When
        $response = $this->signIn($user)->delete(route('subscriptions.plans.destroy', [$subscriptionPlan->uuid]));

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_if_the_user_is_a_staff_member()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->staff(User::SENIOR_STAFF_MEMBER);

        /** @var array $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create();

        // When
        $response = $this->signIn($user)->delete(route('subscriptions.plans.destroy', [$subscriptionPlan->uuid]));

        // Assert
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('subscription_plans', [
            'uuid' => $subscriptionPlan->uuid,
        ]);
    }
}