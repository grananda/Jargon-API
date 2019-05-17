<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::put('/active-subscriptions/upgrade', 'Subscription\ActiveSubscriptionUpgradeController@update')->name('activeSubscriptions.upgrade.update');
    Route::put('/active-subscriptions/downgrade', 'Subscription\ActiveSubscriptionDowngradeController@update')->name('activeSubscriptions.downgrade.update');
});
