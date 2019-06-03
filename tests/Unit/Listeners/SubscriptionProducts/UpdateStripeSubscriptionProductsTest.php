<?php

namespace Tests\Unit\Listeners\SubscriptionProducts;

use App\Events\SubscriptionProduct\SubscriptionProductWasCreated;
use App\Events\SubscriptionProduct\SubscriptionProductWasUpdated;
use App\Listeners\SubscriptionProducts\UpdateStripeSubscriptionProduct;
use App\Models\Subscriptions\SubscriptionProduct;
use App\Repositories\Stripe\StripeSubscriptionProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * @group unit
 * @coversNothing
 */
class UpdateStripeSubscriptionProductsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_stripe_plan_is_deleted()
    {
        // Given
        Event::fake(SubscriptionProductWasCreated::class);

        /** @var \App\Models\Subscriptions\SubscriptionProduct $subscriptionProduct */
        $subscriptionProduct = factory(SubscriptionProduct::class)->create();

        /** @var \App\Events\SubscriptionProduct\SubscriptionProductWasUpdated $event */
        $event = new SubscriptionProductWasUpdated($subscriptionProduct);

        $stripeProductRepository = $this->createMock(StripeSubscriptionProductRepository::class);
        $stripeProductRepository->method('update')
            ->willReturn($this->loadFixture('stripe/product.update.success'))
        ;

        /** @var \App\Listeners\SubscriptionProducts\UpdateStripeSubscriptionProduct $listener */
        $listener = new UpdateStripeSubscriptionProduct($stripeProductRepository);

        // When
        $listener->handle($event);

        // Then
        Event::assertDispatched(SubscriptionProductWasCreated::class);
    }
}
