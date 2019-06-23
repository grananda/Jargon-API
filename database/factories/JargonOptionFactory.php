<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Translations\JargonOption;
use App\Models\Translations\Project;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* @var Factory $factory */
$factory->define(JargonOption::class, function (Faker $faker) {
    return [
        'file_ext'              => 'php',
        'language'              => 'php',
        'framework'             => 'laravel',
        'i18n_path'             => 'resources/lang/',
        'translation_file_mode' => 'array',
        'project_id'            => function () {
            return factory(Project::class)->create()->id;
        },
    ];
});
