<?php

use Illuminate\Support\Facades\Route;

Route::middleware('staff')->group(function () {
    Route::put('/active-subscriptions/upgrade', 'Subscription\ActiveSubscriptionUpgradeController@update')->name('active.subscription.upgrade.update');
    Route::put('/active-subscriptions/downgrade', 'Subscription\ActiveSubscriptionDowngradeController@update')->name('active.subscription.downgrade.update');
});
