<?php

use App\Models\Subscriptions\SubscriptionPlan;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* @var Factory $factory */
$factory->define(SubscriptionPlan::class, function (Faker $faker) {
    return [
        'title'       => $faker->word,
        'description' => $faker->text,
        'alias'       => $faker->word,
        'amount'      => $faker->numberBetween(0, 50),
        'level'       => $faker->numberBetween(50, 100),
        'trial'       => false,
        'status'      => true,
        'created_at'  => Carbon::now(),
        'updated_at'  => Carbon::now(),
    ];
});
