<?php

use App\Models\Subscriptions\SubscriptionOptions;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* @var Factory $factory */
$factory->define(SubscriptionOptions::class, function (Faker $faker) {
    return [
        'title'                => $faker->title,
        'description'          => $faker->text,
        'description_template' => $faker->text,
        'option_key'           => $faker->word,
        'created_at'           => Carbon::now(),
        'updated_at'           => Carbon::now(),
    ];
});
