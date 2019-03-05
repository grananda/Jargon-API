<?php

use App\Models\User;
use App\Models\UserProfile;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* @var Factory $factory */
$factory->define(UserProfile::class, function (Faker $faker) {
    return [
        'username'        => $faker->userName,
        'city'            => $faker->city,
        'country'         => $faker->country,
        'company'         => $faker->company,
        'occupation'      => $faker->word,
        'biography'       => $faker->text(500),
        'web_url'         => $faker->url,
        'social_twitter'  => $faker->userName,
        'social_facebook' => $faker->url,
        'social_git'      => $faker->userName,
        'user_id'         => function () {
            return factory(User::class)->create()->id;
        },
    ];
});
