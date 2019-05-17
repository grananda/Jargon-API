<?php

use Illuminate\Support\Facades\Route;

Route::get('/subscriptions/plans', 'Subscription\SubscriptionPlanController@index')->name('subscriptions.plans.index');
Route::get('/subscriptions/plans/{id}', 'Subscription\SubscriptionPlanController@show')->name('subscriptions.plans.show');

Route::middleware('staff')->group(function () {
    Route::post('/subscriptions/plans', 'Subscription\SubscriptionPlanController@store')->name('subscriptions.plans.store');
    Route::put('/subscriptions/plans/{id}', 'Subscription\SubscriptionPlanController@update')->name('subscriptions.plans.update');
    Route::delete('/subscriptions/plans/{id}', 'Subscription\SubscriptionPlanController@destroy')->name('subscriptions.plans.destroy');
});
