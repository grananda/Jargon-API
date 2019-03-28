<?php

use App\Models\Subscriptions\SubscriptionOption;

class SubscriptionOptionsSeeder extends AbstractSeeder
{
    public function run()
    {
        $this->truncateTables(['subscription_options']);

        $subscriptionOptions = $this->getSeedFileContents('subscriptionOptions');

        foreach ($subscriptionOptions as $subscriptionOption) {
            factory(SubscriptionOption::class)->create($subscriptionOption);
        }
    }
}
