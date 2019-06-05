<?php

namespace Tests\External\Repositories;

use App\Events\SubscriptionPlan\SubscriptionPlanWasCreated;
use App\Events\SubscriptionPlan\SubscriptionPlanWasDeleted;
use App\Events\SubscriptionProduct\SubscriptionProductWasCreated;
use App\Models\Currency;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\Subscriptions\SubscriptionProduct;
use App\Repositories\Stripe\StripeSubscriptionPlanRepository;
use App\Repositories\Stripe\StripeSubscriptionProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * @group external
 * @covers \App\Repositories\Stripe\StripeSubscriptionPlanRepository
 */
class StripeSubscriptionPlanRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creates_updates_and_deletes_a_stripe_plan()
    {
        // Given
        Event::fake([
            SubscriptionProductWasCreated::class,
            SubscriptionPlanWasCreated::class,
            SubscriptionPlanWasDeleted::class,
        ]);

        /** @var \App\Models\Currency $currency */
        $currency = Currency::where('code', 'EUR')->first();

        /** @var \App\Models\Subscriptions\SubscriptionProduct $product */
        $product = factory(SubscriptionProduct::class)->create();

        /** @var \App\Models\Subscriptions\SubscriptionPlan $plan */
        $plan = factory(SubscriptionPlan::class)->create(['is_active' => true]);
        $plan->product()->associate($product);
        $plan->currency()->associate($currency);
        $plan->save();

        /** @var StripeSubscriptionProductRepository $stripeProductRepo */
        $stripeProductRepo = resolve(StripeSubscriptionProductRepository::class);

        /** @var StripeSubscriptionPlanRepository $stripePlanRepo */
        $stripePlanRepo = resolve(StripeSubscriptionPlanRepository::class);

        // When
        $responseCreateProduct = $stripeProductRepo->create($product);
        $responseCreatePlan    = $stripePlanRepo->create($plan);

        $plan->is_active = false;
        $plan->save();
        $responseUpdatePlan = $stripePlanRepo->update($plan);

        $responseDeletePlan = $stripePlanRepo->delete($plan);
        $plan->delete();

        $responseDeleteProduct = $stripeProductRepo->delete($product);

        // Then
        $this->assertSame($responseCreateProduct['id'], $product->alias);
        $this->assertTrue($responseCreatePlan['active']);
        $this->assertFalse($responseUpdatePlan['active']);
        $this->assertTrue($responseDeletePlan);
        $this->assertTrue($responseDeleteProduct);
    }
}
