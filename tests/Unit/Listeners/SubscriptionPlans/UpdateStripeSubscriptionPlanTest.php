<?php


namespace Tests\Unit\Listeners\SubscriptionPlans;


use App\Events\SubscriptionPlan\SubscriptionPlanWasCreated;
use App\Events\SubscriptionPlan\SubscriptionPlanWasUpdated;
use App\Listeners\UpdateStripeSubscriptionPlan;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Repositories\Stripe\StripePlanRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UpdateStripeSubscriptionPlanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_stripe_plan_is_deleted()
    {
        // Given
        Event::fake(SubscriptionPlanWasCreated::class);

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create();

        /** @var \App\Events\SubscriptionPlan\SubscriptionPlanWasUpdated $event */
        $event = new SubscriptionPlanWasUpdated($subscriptionPlan);

        $stripePlanRepository = $this->createMock(StripePlanRepository::class);
        $stripePlanRepository->method('update')
            ->willReturn($this->loadFixture('stripe/plan.update.success'));

        /** @var \App\Listeners\UpdateStripeSubscriptionPlan $listener */
        $listener = new UpdateStripeSubscriptionPlan($stripePlanRepository);

        // When
        $listener->handle($event);

        // Then
        Event::assertDispatched(SubscriptionPlanWasCreated::class);
    }
}