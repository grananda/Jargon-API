<?php

use App\Models\Subscriptions\SubscriptionProduct;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

/* @var Factory $factory */
$factory->define(SubscriptionProduct::class, function (Faker $faker) {
    return [
        'title'       => $faker->word,
        'description' => $faker->text,
        'alias'       => Str::slug($faker->sentence(3)),
        'rank'        => $faker->numberBetween(10, 100),
        'is_active'   => true,
        'created_at'  => Carbon::now(),
        'updated_at'  => Carbon::now(),
    ];
});
