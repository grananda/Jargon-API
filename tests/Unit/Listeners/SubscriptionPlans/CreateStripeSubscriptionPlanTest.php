<?php

namespace Tests\Unit\Listeners\SubscriptionPlans;

use App\Events\SubscriptionPlan\SubscriptionPlanWasCreated;
use App\Listeners\SubscriptionPlans\CreateStripeSubscriptionPlan;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Repositories\Stripe\StripeSubscriptionPlanRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * @group unit
 * @coversNothing
 */
class CreateStripeSubscriptionPlanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_stripe_plan_is_created()
    {
        // Given
        Event::fake(SubscriptionPlanWasCreated::class);

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create();

        /** @var \App\Events\SubscriptionPlan\SubscriptionPlanWasCreated $event */
        $event = new SubscriptionPlanWasCreated($subscriptionPlan);

        $stripePlanRepository = $this->createMock(StripeSubscriptionPlanRepository::class);
        $stripePlanRepository->method('create')
            ->willReturn($this->loadFixture('stripe/plan.create.success'))
        ;

        /** @var \App\Listeners\SubscriptionPlans\CreateStripeSubscriptionProduct $listener */
        $listener = new CreateStripeSubscriptionPlan($stripePlanRepository);

        // When
        $listener->handle($event);

        // Then
        Event::assertDispatched(SubscriptionPlanWasCreated::class);
    }
}
