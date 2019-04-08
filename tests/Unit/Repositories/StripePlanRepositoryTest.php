<?php


namespace Tests\Unit\Services;


use App\Events\SubscriptionPlan\SubscriptionPlanWasCreated;
use App\Events\SubscriptionPlan\SubscriptionPlanWasUpdated;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Repositories\Stripe\StripePlanRepository;
use App\Repositories\SubscriptionPlanRepository;
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

        /** @var StripePlanRepository $stripePlanRepository */
        $stripePlanRepository = new StripePlanRepository();

        // When
        /** @var array $response */
        $responseCreate = $stripePlanRepository->create($subscriptionPlan);

        /** @var array $responseDelete */
        $responseDelete = $stripePlanRepository->delete($subscriptionPlan);

        // Then
        $this->assertEquals($responseCreate['id'], $subscriptionPlan->alias);
        $this->assertTrue($responseDelete);
    }

    /** @test */
    public function a_stripe_plan_is_updated_and_removed()
    {
        // Given
        Event::fake(SubscriptionPlanWasCreated::class);
        Event::fake(SubscriptionPlanWasUpdated::class);

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create();

        /** @var StripePlanRepository $stripePlanRepository */
        $stripePlanRepository = new StripePlanRepository();

        /** @var SubscriptionPlanRepository $subscriptionPlanRepository */
        $subscriptionPlanRepository = resolve(SubscriptionPlanRepository::class);

        /** @var array $response */
        $responseCreate = $stripePlanRepository->create($subscriptionPlan);

        $title = $this->faker->word;

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = $subscriptionPlanRepository->updateSubscriptionPlan($subscriptionPlan, [
            'title' => $title,
        ]);

        // When
        /** @var array $response */
        $responseUpdate = $stripePlanRepository->update($subscriptionPlan);

        /** @var array $responseDelete */
        $responseDelete = $stripePlanRepository->delete($subscriptionPlan);

        // Then
        $this->assertEquals(0, strpos($responseUpdate['nickname'], $title));
        $this->assertTrue($responseDelete);
    }
}