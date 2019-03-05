<?php

use App\Models\Memo;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

/* @var Factory $factory */
$factory->define(Memo::class, function (Faker $faker) {
    return [
        'subject' => $faker->sentence(rand(3, 4)),
        'body'    => $faker->paragraphs(5, true),
        'status'  => $faker->randomElement([
            'draft',
            'sent',
        ]),
        'user_id'    => null,
        'item_token' => Str::random(Memo::ITEM_TOKEN_LENGTH),
        'created_at' => Carbon::now()->subDays(rand(1, 30)),
        'updated_at' => Carbon::now(),
    ];
});
