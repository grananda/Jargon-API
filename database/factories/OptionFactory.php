<?php

use App\Models\Options\Option;
use App\Models\Options\OptionCategory;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

/* @var Factory $factory */
$factory->define(Option::class, function (Faker $faker) {
    return [
        'title'              => $faker->title,
        'description'        => $faker->text,
        'option_category_id' => function () {
            return factory(OptionCategory::class)->create()->id;
        },
        'option_key'   => Str::slug($faker->sentence(3)),
        'option_value' => $faker->word,
        'option_scope' => $faker->randomElement(['user', 'staff']),
        'option_type'  => $faker->randomElement(['check', 'text']),
        'created_at'   => Carbon::now(),
        'updated_at'   => Carbon::now(),
    ];
});
