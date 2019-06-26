<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Dialect;
use App\Models\Translations\GitFileHash;
use App\Models\Translations\Project;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* @var Factory $factory */
$factory->define(GitFileHash::class, function (Faker $faker) {
    return [
        'locale' => function () {
            return Dialect::inRandomOrder()->first()->locale;
        },
        'file'                => implode('.', $faker->words(2)),
        'hash'                => $faker->sha1,
        'pull_request_number' => $faker->randomNumber(),
        'project_id'          => function () {
            return factory(Project::class)->create()->id;
        },
    ];
});
