<?php

use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\Subscriptions\SubscriptionPlanOptionValue;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* @var Factory $factory */
$factory->define(SubscriptionPlanOptionValue::class, function (Faker $faker) {
    return [
        'option_key'   => $faker->word,
        'option_value' => $faker->word,
    ];
});
