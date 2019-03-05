<?php

use App\Models\User;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

/* @var Factory $factory */
$factory->define(User::class, function (Faker $faker) {
    return [
        'name'              => $faker->name,
        'email'             => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password'          => bcrypt($faker->password),
        'remember_token'    => Str::random(10),
        'created_at'        => Carbon::now(),
        'updated_at'        => Carbon::now(),
        'activated_at'      => Carbon::now(),
    ];
});
