<?php

use Illuminate\Support\Facades\Route;

Route::middleware('staff')->group(function () {
    Route::get('/subscriptions/plans/options/', 'Subscription\SubscriptionPlanController@index')->name('subscriptions.plans.options.index');
    Route::post('/subscriptions/plans/options', 'Subscription\SubscriptionPlanController@store')->name('subscriptions.plans.options.store');
    Route::put('/subscriptions/plans/options/{id}', 'Subscription\SubscriptionPlanController@update')->name('subscriptions.plans.options.update');
    Route::delete('/subscriptions/plans/options/{id}', 'Subscription\SubscriptionPlanController@destroy')->name('subscriptions.plans.options.destroy');
});
