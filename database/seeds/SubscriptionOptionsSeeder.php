<?php

use App\Models\Subscriptions\SubscriptionOptions;

class SubscriptionOptionsSeeder extends AbstractSeeder
{
    public function run()
    {
        $this->truncateTables(['subscription_options']);

        $subscriptionOptions = $this->getSeedFileContents('subscriptionOptions');

        foreach ($subscriptionOptions as $subscriptionOption) {
            factory(SubscriptionOptions::class)->create($subscriptionOption);
        }
    }
}
