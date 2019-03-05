<?php

use App\Models\Subscriptions\ActiveSubscription;
use App\Models\Subscriptions\ActiveSubscriptionOptionValue;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run()
    {
        $users            = User::get();
        $subscriptionPlan = SubscriptionPlan::where('alias', '=', 'basic')->firstOrFail();

        foreach ($users as $user) {
            $subscription = ActiveSubscription::create([
                'user_id'              => $user->id,
                'subscription_plan_id' => $subscriptionPlan->id,
            ]);

            foreach ($subscriptionPlan->options as $option) {
                ActiveSubscriptionOptionValue::create([
                    'active_subscription_id' => $subscription->id,
                    'option_key'             => $option->option_key,
                    'option_value'           => $option->option_value,
                ]);
            }
        }
    }
}
