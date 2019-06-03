<?php

namespace Tests\Feature\SubscriptionPlan;

use App\Models\Subscriptions\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group feature
 * @covers \App\Http\Controllers\Subscription\SubscriptionPlanController::show
 */
class ShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_200_will_be_returned_when_requesting_a_subscription_plan()
    {
        // Given
        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create();

        // When
        $response = $this->get(route('subscriptions.plans.show', [$subscriptionPlan->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['alias' => $subscriptionPlan->alias]);
    }
}
