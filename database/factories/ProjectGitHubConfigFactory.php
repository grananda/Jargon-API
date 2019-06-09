<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Translations\Project;
use App\Models\Translations\ProjectGitHubConfig;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* @var Factory $factory */
$factory->define(ProjectGitHubConfig::class, function (Faker $faker) {
    return [
        'username'     => $faker->userName,
        'repository'   => implode('-', $faker->words(3)),
        'base_branch'  => 'heads/master',
        'access_token' => $faker->linuxPlatformToken,
        'project_id'   => function () {
            return factory(Project::class)->create()->id;
        },
    ];
});
