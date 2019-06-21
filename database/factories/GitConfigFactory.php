<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Translations\GitConfig;
use App\Models\Translations\Project;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* @var Factory $factory */
$factory->define(GitConfig::class, function (Faker $faker) {
    return [
        'username'     => $faker->userName,
        'email'        => $faker->email,
        'repository'   => implode('-', $faker->words(3)),
        'base_branch'  => 'master',
        'access_token' => $faker->linuxPlatformToken,
        'project_id'   => function () {
            return factory(Project::class)->create()->id;
        },
    ];
});
