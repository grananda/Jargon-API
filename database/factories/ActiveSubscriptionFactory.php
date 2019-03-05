<?php

use App\Models\Subscriptions\ActiveSubscription;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* @var Factory $factory */
$factory->define(ActiveSubscription::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'subscription_plan_id' => function () {
            return SubscriptionPlan::inRandomOrder()->first()->id;
        },
        'ends_at'    => Carbon::now()->addMonth(1),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ];
});
