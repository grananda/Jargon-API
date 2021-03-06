<?php

use App\Models\Translations\Project;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* @var Factory $factory */
$factory->define(Project::class, function (Faker $faker) {
    return [
        'title'           => $faker->sentence(rand(2, 3)),
        'description'     => $faker->sentence(20),
        'organization_id' => null,
    ];
});
