<?php

use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\Subscriptions\SubscriptionPlanOptionValue;

class SubscriptionPlansSeeder extends AbstractSeeder
{
    public function run()
    {
        $this->truncateTables(['subscription_plans', 'subscription_plan_option_values']);

        $plans = $this->getSeedFileContents('subscriptionPlans');

        foreach ($plans as $plan) {
            $subscriptionPlan = factory(SubscriptionPlan::class)->create([
                'title'       => $plan['title'],
                'description' => $plan['description'],
                'alias'       => $plan['alias'],
                'level'       => $plan['level'],
                'amount'      => $plan['amount'],
                'trial'       => false,
            ]);

            foreach ($plan['options'] as $option) {
                factory(SubscriptionPlanOptionValue::class)->create([
                    'subscription_plan_id' => $subscriptionPlan->id,
                    'option_key'           => $option['option_key'],
                    'option_value'         => $option['option_value'],
                ]);
            }
        }
    }
}
