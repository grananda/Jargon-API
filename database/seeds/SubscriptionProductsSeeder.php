<?php

use App\Events\SubscriptionPlan\SubscriptionPlanWasCreated;
use App\Events\SubscriptionProduct\SubscriptionProductWasCreated;
use App\Models\Currency;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\Subscriptions\SubscriptionPlanOptionValue;
use App\Models\Subscriptions\SubscriptionProduct;
use Illuminate\Support\Facades\Event;

class SubscriptionProductsSeeder extends AbstractSeeder
{
    public function run()
    {
        $this->truncateTables(['subscription_products', 'subscription_plans', 'subscription_plan_option_values']);

        Event::fake(SubscriptionPlanWasCreated::class);
        Event::fake(SubscriptionProductWasCreated::class);

        $products = $this->getSeedFileContents('subscriptionProducts');

        foreach ($products as $product) {
            $subscriptionProduct = factory(SubscriptionProduct::class)->create([
                'title'       => $product['title'],
                'description' => $product['description'],
                'alias'       => $product['alias'],
                'rank'        => $product['rank'],
            ]);
            foreach ($product['plans'] as $plan) {
                $subscriptionPlan = factory(SubscriptionPlan::class)->create([
                    'alias'       => $plan['alias'],
                    'amount'      => $plan['amount'],
                    'sort_order'  => $plan['sort_order'],
                    'interval'    => $plan['interval'],
                    'currency_id' => Currency::where('code', $plan['currency'])->first(),
                    'product_id'  => $subscriptionProduct->id,
                ]);

                foreach ($product['options'] as $option) {
                    factory(SubscriptionPlanOptionValue::class)->create([
                        'subscription_plan_id' => $subscriptionPlan->id,
                        'option_key'           => $option['option_key'],
                        'option_value'         => $option['option_value'],
                    ]);
                }
            }
        }
    }
}
