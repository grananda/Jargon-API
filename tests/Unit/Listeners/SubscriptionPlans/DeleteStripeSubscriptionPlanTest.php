<?php


namespace Tests\Unit\Listeners\SubscriptionPlans;


use App\Events\SubscriptionPlan\SubscriptionPlanWasCreated;
use App\Listeners\SubscriptionPlans\DeleteStripeSubscriptionPlan;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Repositories\Stripe\StripeSubscriptionPlanRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DeleteStripeSubscriptionPlanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_stripe_plan_is_deleted()
    {
        // Given
        Event::fake(SubscriptionPlanWasCreated::class);

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create();

        /** @var \App\Events\SubscriptionPlan\SubscriptionPlanWasCreated $event */
        $event = new SubscriptionPlanWasCreated($subscriptionPlan);

        $stripePlanRepository = $this->createMock(StripeSubscriptionPlanRepository::class);
        $stripePlanRepository->method('delete')
            ->willReturn($this->loadFixture('stripe/plan.create.success'));

        /** @var \App\Listeners\SubscriptionPlans\CreateStripeSubscriptionProduct $listener */
        $listener = new DeleteStripeSubscriptionPlan($stripePlanRepository);

        // When
        $listener->handle($event);

        // Then
        Event::assertDispatched(SubscriptionPlanWasCreated::class);
    }
}