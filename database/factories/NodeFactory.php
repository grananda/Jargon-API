<?php

use App\Models\Translations\Node;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* @var Factory $factory */
$factory->define(Node::class, function (Faker $faker) {
    return [
        'key'        => $faker->word,
        'route'      => null,
        'sort_index' => 0,
        'project_id' => null,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ];
});
