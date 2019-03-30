<?php


namespace Tests\Unit\Models;


use App\Models\Subscriptions\SubscriptionOption;
use App\Models\Subscriptions\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionPlanModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creates_a_subscription_plan_with_option_values()
    {
        // Given
        /** @var \App\Models\Subscriptions\SubscriptionOption $options */
        $option = factory(SubscriptionOption::class)->create();

        /** @var  \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create();

        $optionValue = $this->faker->numberBetween(5, 10);

        // When
        $subscriptionPlan = $subscriptionPlan->addOption($option, $optionValue);

        // Then
        $this->assertEquals(1, $subscriptionPlan->options()->count());
        $this->assertEquals($option->option_key, $subscriptionPlan->options()->first()->key->option_key);
        $this->assertDatabaseHas('subscription_plan_option_values', [
            'subscription_plan_id' => $subscriptionPlan->id,
            'option_key'           => $option->option_key,
            'option_value'         => $optionValue,
        ]);
    }
}