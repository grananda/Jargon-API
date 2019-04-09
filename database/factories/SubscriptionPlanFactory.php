<?php

use App\Models\Currency;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\Subscriptions\SubscriptionProduct;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

/* @var Factory $factory */
$factory->define(SubscriptionPlan::class, function (Faker $faker) {
    return [
        'product_id' => function () {
            return factory(SubscriptionProduct::class)->create()->id;
        },
        'currency_id' => Currency::where('code', 'EUR')->first()->id,
        'alias'       => Str::slug($faker->sentence(3)),
        'amount'      => $faker->numberBetween(0, 50),
        'sort_order'  => $faker->numberBetween(0, 10),
        'interval'    => $faker->randomElement(['month', 'year']),
        'is_active'   => true,
        'created_at'  => Carbon::now(),
        'updated_at'  => Carbon::now(),
    ];
});
