<?php


namespace Tests\Unit\Listeners\SubscriptionPlans;


use App\Models\Subscriptions\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Cashier;
use Stripe\Plan;
use Stripe\Product;
use Stripe\Stripe;
use Tests\TestCase;

/**
 * @group third-party-api
 */
class StripePlanTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function a_stripe_plan_is_created()
    {
        // Given
        Stripe::setApiKey(config('services.stripe.secret'));

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->make();

        // When
        /** @var \Stripe\ApiResource $responseCreate */
        $responseCreatePlan = Plan::create([
            'id'         => $subscriptionPlan->alias,
            "nickname"   => $subscriptionPlan->title . " Monthly Subscription",
            "product"    => [
                'name' => $subscriptionPlan->title,
                'type' => SubscriptionPlan::STANDARD_STRIPE_TYPE_LABEL,
            ],
            "amount"     => $subscriptionPlan->amount,
            "currency"   => Cashier::usesCurrency(),
            "interval"   => SubscriptionPlan::STANDARD_STRIPE_BILLING_PERIOD,
            "usage_type" => SubscriptionPlan::STANDARD_STRIPE_BILLING_USAGE_TYPE,
        ]);

        /** @var \Stripe\Plan $plan */
        $plan = Plan::retrieve($responseCreatePlan->id);

        /** @var \Stripe\Product $product */
        $product = Product::retrieve($responseCreatePlan->product);

        /** @var \Stripe\ApiResource $responseDelete */
        $responseDeletePlan = $plan->delete();

        /** @var \Stripe\ApiResource $responseDelete */
        $responseDeleteProduct = $product->delete();

        // Then
        $this->assertNotNull($responseCreatePlan->id);

        $this->assertEquals($responseCreatePlan->id, $subscriptionPlan->alias);

        $this->assertTrue($responseDeletePlan->isDeleted());
        $this->assertTrue($responseDeleteProduct->isDeleted());
    }
}