<?php

namespace Tests\Feature\SubscriptionProduct;

use App\Models\Subscriptions\SubscriptionProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group feature
 * @covers \App\Http\Controllers\Subscription\SubscriptionProductController::show
 */
class ShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_200_will_be_returned_when_requesting_a_subscription_product()
    {
        // Given
        /** @var \App\Models\Subscriptions\SubscriptionProduct $subscriptionProduct */
        $subscriptionProduct = factory(SubscriptionProduct::class)->create();

        // When
        $response = $this->get(route('subscriptions.products.show', [$subscriptionProduct->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['alias' => $subscriptionProduct->alias]);
    }
}
