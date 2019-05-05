<?php

namespace Tests\Feature\SubscriptionProduct;

use App\Events\SubscriptionProduct\SubscriptionProductWasCreated;
use App\Events\SubscriptionProduct\SubscriptionProductWasUpdated;
use App\Models\Subscriptions\SubscriptionProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * @coversNothing
 */
class StoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->post(route('subscriptions.products.store'));

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
        $response = $this->signIn($user)->post(route('subscriptions.products.store'));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_is_not_a_senior_staff_member()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->staff(User::JUNIOR_STAFF_MEMBER);

        /** @var \App\Models\Subscriptions\SubscriptionProduct $product */
        $product = factory(SubscriptionProduct::class)->make();

        // When
        $response = $this->signIn($user)->post(route('subscriptions.products.store'), $product->toArray());

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_if_the_user_is_a_staff_member()
    {
        // Given
        Event::fake([SubscriptionProductWasUpdated::class, SubscriptionProductWasCreated::class]);

        /** @var \App\Models\User $user */
        $user = $this->staff(User::SENIOR_STAFF_MEMBER);

        /** @var array $product */
        $data = factory(SubscriptionProduct::class)->make()->toArray();

        // When
        $response = $this->signIn($user)->post(route('subscriptions.products.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['alias' => $data['alias']]);

        $subscriptionProduct = SubscriptionProduct::findByUuidOrFail($response->json()['data']['id']);

        $this->assertDatabaseHas('subscription_products', [
            'alias' => $subscriptionProduct->alias,
        ]);

        Event::assertDispatched(SubscriptionProductWasCreated::class);
        Event::assertNotDispatched(SubscriptionProductWasUpdated::class);
    }
}
