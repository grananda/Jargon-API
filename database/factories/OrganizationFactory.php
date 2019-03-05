<?php

use App\Models\Organization;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* @var Factory $factory */
$factory->define(Organization::class, function (Faker $faker) {
    return [
        'name'        => $faker->sentence(2),
        'description' => $faker->text(50),
        'created_at'  => $faker->date(),
        'updated_at'  => $faker->date(),
    ];
});
