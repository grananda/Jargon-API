<?php

namespace Tests\Feature\SubscriptionProduct;

use App\Models\Subscriptions\SubscriptionProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @coversNothing
 */
class ListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_200_code_will_be_returned_when_requesting_subscription_products()
    {
        // Given
        /** @var \App\Models\Subscriptions\SubscriptionProduct $subscriptionProduct */
        $subscriptionProduct = factory(SubscriptionProduct::class)->create();

        // When
        $response = $this->get(route('subscriptions.products.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['alias' => $subscriptionProduct->alias]);
    }
}
