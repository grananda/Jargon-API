<?php

use App\Models\Translations\Translation;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

/* @var Factory $factory */
$factory->define(Translation::class, function (Faker $faker) {
    return [
        'uuid'       => Str::uuid(),
        'definition' => $faker->sentence,
        'node_id'    => null,
        'dialect_id' => null,
    ];
});
