<?php

use App\Models\Options\OptionCategory;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* @var Factory $factory */
$factory->define(OptionCategory::class, function (Faker $faker) {
    return [
        'title'       => $faker->title,
        'description' => $faker->text,
        'created_at'  => Carbon::now(),
        'updated_at'  => Carbon::now(),
    ];
});
