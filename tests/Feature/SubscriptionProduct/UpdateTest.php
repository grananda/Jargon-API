<?php

namespace Tests\Feature\SubscriptionProduct;


use App\Events\SubscriptionPlan\SubscriptionPlanWasUpdated;
use App\Models\Subscriptions\SubscriptionOption;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\Subscriptions\SubscriptionPlanOptionValue;
use App\Models\Subscriptions\SubscriptionProduct;
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
        $response = $this->put(route('subscriptions.products.update', [123]));

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
        $response = $this->signIn($user)->put(route('subscriptions.products.update', [123]));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_is_not_a_senior_staff_member()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->staff(User::JUNIOR_STAFF_MEMBER);

        /** @var array $subscriptionProduct */
        $subscriptionProduct = factory(SubscriptionProduct::class)->create();

        $data = [
            'status' => false,
        ];

        // When
        $response = $this->signIn($user)->put(route('subscriptions.products.update', [$subscriptionProduct->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_if_the_user_is_a_staff_member()
    {
        // Given
        Event::fake(SubscriptionPlanWasUpdated::class);

        /** @var \App\Models\User $user */
        $user = $this->staff(User::SENIOR_STAFF_MEMBER);

        /** @var array $subscriptionProduct */
        $subscriptionProduct = factory(SubscriptionProduct::class)->create();

        $data = [
            'is_active' => false,
        ];


        // When
        $response = $this->signIn($user)->put(route('subscriptions.products.update', [$subscriptionProduct->uuid]), $data);

        // Assert
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['alias' => $subscriptionProduct->alias]);
        $response->assertJsonFragment(['is_active' => $data['is_active']]);
        $this->assertDatabaseHas('subscription_products', [
            'alias'     => $subscriptionProduct->alias,
            'is_active' => $data['is_active'],
        ]);

        Event::assertDispatched(SubscriptionPlanWasUpdated::class);
    }
}