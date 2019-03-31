<?php

use Illuminate\Support\Facades\Route;

Route::middleware('staff')->group(function () {
    Route::get('/subscriptions/options/', 'Subscription\SubscriptionOptionController@index')->name('subscriptions.options.index');
    Route::post('/subscriptions/options', 'Subscription\SubscriptionOptionController@store')->name('subscriptions.options.store');
    Route::put('/subscriptions/options/{id}', 'Subscription\SubscriptionOptionController@update')->name('subscriptions.options.update');
    Route::delete('/subscriptions/options/{id}', 'Subscription\SubscriptionOptionController@destroy')->name('subscriptions.options.destroy');
});
