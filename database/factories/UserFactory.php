<?php

use App\Models\User;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

/* @var Factory $factory */
$factory->define(User::class, function (Faker $faker) {
    return [
        'name'             => $faker->name,
        'email'            => $faker->unique()->safeEmail,
        'password'         => bcrypt($faker->password),
        'activation_token' => Str::random(32),
        'remember_token'   => Str::random(10),
        'stripe_id'        => 'cus_'.Str::random(24),
        'created_at'       => Carbon::now(),
        'updated_at'       => Carbon::now(),
        'activated_at'     => Carbon::now(),
    ];
});
