<?php

use App\Models\Team;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* @var Factory $factory */
$factory->define(Team::class, function (Faker $faker) {
    return [
        'name'       => $faker->sentence(rand(2, 3)),
        'created_at' => $faker->date(),
        'updated_at' => $faker->date(),
    ];
});
