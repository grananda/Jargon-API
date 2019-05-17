<?php

use App\Models\Currency;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* @var Factory $factory */
$factory->define(Currency::class, function (Faker $faker) {
    return [
        'code' => $faker->currencyCode,
        'name' => $faker->word,
    ];
});
