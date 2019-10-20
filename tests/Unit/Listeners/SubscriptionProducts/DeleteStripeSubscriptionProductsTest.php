<?php

namespace Tests\Unit\Listeners\SubscriptionProducts;

use App\Events\SubscriptionProduct\SubscriptionProductWasCreated;
use App\Events\SubscriptionProduct\SubscriptionProductWasDeleted;
use App\Listeners\SubscriptionProducts\DeleteStripeSubscriptionProduct;
use App\Models\Subscriptions\SubscriptionProduct;
use App\Repositories\Stripe\StripeSubscriptionProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * @group unit
 * @covers \App\Listeners\SubscriptionProducts\DeleteStripeSubscriptionProduct
 */
class DeleteStripeSubscriptionProductsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_stripe_product_is_deleted()
    {
        // Given
        Event::fake(SubscriptionProductWasCreated::class);

        /** @var \App\Models\Subscriptions\SubscriptionProduct $subscriptionProduct */
        $subscriptionProduct = factory(SubscriptionProduct::class)->create();

        /** @var \App\Events\SubscriptionProduct\SubscriptionProductWasDeleted $event */
        $event = new SubscriptionProductWasDeleted($subscriptionProduct);

        /** @var MockObject $stripeProductRepository */
        $stripeProductRepository = $this->createMock(StripeSubscriptionProductRepository::class);
        $stripeProductRepository->method('delete')
            ->willReturn($this->loadFixture('stripe/plan.create.success'))
        ;

        /** @var \App\Listeners\SubscriptionProducts\DeleteStripeSubscriptionProduct $listener */
        $listener = new DeleteStripeSubscriptionProduct($stripeProductRepository);

        // When
        $listener->handle($event);

        // Then
        Event::assertDispatched(SubscriptionProductWasCreated::class);
    }
}
