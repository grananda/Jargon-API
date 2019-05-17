<?php

use App\Models\Options\OptionUser;
use App\Models\User;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* @var Factory $factory */
$factory->define(OptionUser::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'option_value' => $faker->word,
        'option_key'   => $faker->word,
        'created_at'   => Carbon::now(),
        'updated_at'   => Carbon::now(),
    ];
});
