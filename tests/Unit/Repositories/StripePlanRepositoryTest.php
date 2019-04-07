<?php


namespace Tests\Unit\Services;


use App\Events\SubscriptionPlan\SubscriptionPlanWasCreated;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Repositories\Stripe\StripePlanRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * @group third-party-api
 */
class StripePlanRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_stripe_plan_is_created_and_removed()
    {
        // Given
        Event::fake(SubscriptionPlanWasCreated::class);

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create();

        /** @var StripePlanRepository $stripeApiGateway */
        $stripeApiGateway = new StripePlanRepository();

        // When
        /** @var array $response */
        $response = $stripeApiGateway->create($subscriptionPlan);

        $responseDelete = $stripeApiGateway->delete($subscriptionPlan);

        // Then
        $this->assertEquals($response['id'], $subscriptionPlan->alias);
        $this->assertTrue($responseDelete);
    }
}