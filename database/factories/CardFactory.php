<?php

use App\Models\Card;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

/* @var Factory $factory */

$factory->define(Card::class, function (Faker $faker) {
    $cc = $faker->creditCardDetails;

    return [
        'stripe_id' => 'card_'.Str::random(24),
        'brand'     => $cc['type'],
        'country'   => 'ES',
        'user_id'   => function () {
            return factory(User::class)->create()->id;
        },
        'last4'           => substr($cc['number'], -4),
        'exp_month'       => current(explode('/', $cc['expirationDate'])),
        'exp_year'        => last(explode('/', $cc['expirationDate'])),
        'name'            => $faker->name,
        'address_city'    => $faker->city,
        'address_country' => $faker->country,
        'address_line1'   => $faker->streetAddress,
        'address_line2'   => $faker->address,
        'address_state'   => $faker->word,
        'address_zip'     => $faker->numberBetween(1000, 2000),
    ];
});
