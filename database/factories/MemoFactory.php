<?php

use App\Models\Communications\Memo;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* @var Factory $factory */
$factory->define(Memo::class, function (Faker $faker) {
    return [
        'uuid'       => $faker->uuid,
        'subject'    => $faker->sentence(rand(3, 4)),
        'body'       => $faker->paragraphs(5, true),
        'status'     => 'sent',
        'created_at' => Carbon::now()->subDays(rand(1, 30)),
        'updated_at' => Carbon::now(),
    ];
});
