<?php

namespace Tests\Unit\Listeners\SubscriptionPlans;

use App\Events\SubscriptionProduct\SubscriptionProductWasCreated;
use App\Listeners\SubscriptionProducts\CreateStripeSubscriptionProduct;
use App\Models\Subscriptions\SubscriptionProduct;
use App\Repositories\Stripe\StripeSubscriptionProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * @group unit
 * @covers \App\Listeners\SubscriptionProducts\CreateStripeSubscriptionProduct
 */
class CreateStripeSubscriptionProductsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_stripe_plan_is_created()
    {
        // Given
        Event::fake(SubscriptionProductWasCreated::class);

        /** @var \App\Models\Subscriptions\SubscriptionProduct $subscriptionProduct */
        $subscriptionProduct = factory(SubscriptionProduct::class)->create();

        /** @var \App\Events\SubscriptionPlan\SubscriptionPlanWasCreated $event */
        $event = new SubscriptionProductWasCreated($subscriptionProduct);

        $stripeProductRepository = $this->createMock(StripeSubscriptionProductRepository::class);
        $stripeProductRepository->method('create')
            ->willReturn($this->loadFixture('stripe/product.create.success'))
        ;

        /** @var CreateStripeSubscriptionProduct $listener */
        $listener = new CreateStripeSubscriptionProduct($stripeProductRepository);

        // When
        $listener->handle($event);

        // Then
        Event::assertDispatched(SubscriptionProductWasCreated::class);
    }
}
