<?php

namespace Tests\Feature\SubscriptionProduct;


use App\Events\SubscriptionProduct\SubscriptionProductWasDeleted;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\Subscriptions\SubscriptionProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

class DeleteTest extends TestCase
{
    use RefreshDatabase,
        CreateActiveSubscription;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->delete(route('subscriptions.products.destroy', [123]));

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
        $response = $this->signIn($user)->delete(route('subscriptions.products.destroy', [123]));

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
        $subscriptionProduct = factory(SubscriptionProduct::class)->create();

        // When
        $response = $this->signIn($user)->delete(route('subscriptions.products.destroy', [$subscriptionProduct->uuid]));

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_when_active_plans_in_subscription_product()
    {
        // Given
        /** @var \App\Models\User $user */
        $staff = $this->staff(User::SENIOR_STAFF_MEMBER);

        /** @var array $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create();

        // When
        $response = $this->signIn($staff)->delete(route('subscriptions.products.destroy', [$subscriptionPlan->product->uuid]));

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_if_the_user_is_a_staff_member()
    {
        // Given
        Event::fake(SubscriptionProductWasDeleted::class);

        /** @var \App\Models\User $user */
        $user = $this->staff(User::SENIOR_STAFF_MEMBER);

        /** @var \App\Models\Subscriptions\SubscriptionProduct $subscriptionProduct */
        $subscriptionProduct = factory(SubscriptionProduct::class)->create();

        // When
        $response = $this->signIn($user)->delete(route('subscriptions.products.destroy', [$subscriptionProduct->uuid]));

        // Assert
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('subscription_products', [
            'uuid' => $subscriptionProduct->uuid,
        ]);

        Event::assertDispatched(SubscriptionProductWasDeleted::class);
    }
}