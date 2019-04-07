<?php


namespace Tests\Unit\Listeners\SubscriptionPlans;


use App\Models\Subscriptions\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\Plan;
use Stripe\Product;
use Stripe\Stripe;
use Tests\TestCase;

/**
 * @group third-party-api
 */
class CreateStripeSubscriptionPlanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_stripe_product_is_created()
    {
        // Given
        Stripe::setApiKey(config('services.stripe.secret'));

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->make();

        // When

        /** @var \Stripe\ApiResource $responseCreate */
        $responseCreate = Product::create([
            'name' => $subscriptionPlan->title,
            'type' => 'service',
        ]);

        /** @var \Stripe\Product $product */
        $product = Product::retrieve($responseCreate->id);

        /** @var \Stripe\ApiResource $responseDelete */
        $responseDelete = $product->delete();

        // Then
        $this->assertNotNull($responseCreate->id);
        $this->assertEquals($responseCreate->id, $responseDelete->id);
        $this->assertTrue($responseDelete->isDeleted());
    }

    /** @test */
    public function a_stripe_plan_is_created()
    {
        // Given
        Stripe::setApiKey(config('services.stripe.secret'));

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->make();

        // When

        /** @var \Stripe\ApiResource $responseCreate */
        $responseCreateProduct = Product::create([
            'name' => $subscriptionPlan->title,
            'type' => 'service',
        ]);

        /** @var \Stripe\Product $product */
        $product = Product::retrieve($responseCreateProduct->id);

        /** @var \Stripe\ApiResource $responseCreate */
        $responseCreatePlan = Plan::create([
            "nickname"   => $subscriptionPlan->title." Monthly",
            "product"    => $responseCreateProduct->id,
            "amount"     => $subscriptionPlan->amount,
            "currency"   => "eur",
            "interval"   => "month",
            "usage_type" => "licensed",
        ]);

        /** @var \Stripe\Plan $plan */
        $plan = Plan::retrieve($responseCreatePlan->id);

        /** @var \Stripe\ApiResource $responseDelete */
        $responseDeletePlan = $plan->delete();

        /** @var \Stripe\ApiResource $responseDelete */
        $responseDeleteProduct = $product->delete();

        // Then
        $this->assertNotNull($responseCreateProduct->id);
        $this->assertNotNull($responseCreatePlan->id);

        $this->assertEquals($responseCreateProduct->id, $responseDeleteProduct->id);
        $this->assertEquals($responseCreatePlan->id, $responseDeletePlan->id);

        $this->assertTrue($responseDeletePlan->isDeleted());
        $this->assertTrue($responseDeleteProduct->isDeleted());
    }
}