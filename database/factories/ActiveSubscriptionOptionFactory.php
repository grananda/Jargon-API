<?php

use App\Models\Subscriptions\ActiveSubscription;
use App\Models\Subscriptions\ActiveSubscriptionOptionValue;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* @var Factory $factory */
$factory->define(ActiveSubscriptionOptionValue::class, function (Faker $faker) {
    return [
        'active_subscription_id' => function () {
            return factory(ActiveSubscription::class)->create()->id;
        },
        'option_key'   => $faker->word,
        'option_value' => $faker->word,
        'created_at'   => Carbon::now(),
        'updated_at'   => Carbon::now(),
    ];
});
