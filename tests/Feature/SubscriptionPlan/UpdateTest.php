<?php

namespace Tests\Feature\SubscriptionPlan;


use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->put(route('subscriptions.plans.update', [123]));

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
        $response = $this->put(route('subscriptions.plans.update', [123]));

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

        $data = [
            'status' => false,
        ];

        // When
        $response = $this->signIn($user)->put(route('subscriptions.plans.update', [$subscriptionPlan->uuid]), $data);

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

        $data = [
            'status' => false,
        ];

        // When
        $response = $this->signIn($user)->put(route('subscriptions.plans.update', [$subscriptionPlan->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['alias' => $subscriptionPlan->alias]);
        $response->assertJsonFragment(['status' => $data['status']]);
    }
}