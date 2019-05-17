<?php

use Illuminate\Support\Facades\Route;

Route::get('/subscriptions/products', 'Subscription\SubscriptionProductController@index')->name('subscriptions.products.index');
Route::get('/subscriptions/products/{id}', 'Subscription\SubscriptionProductController@show')->name('subscriptions.products.show');

Route::middleware('staff')->group(function () {
    Route::post('/subscriptions/products', 'Subscription\SubscriptionProductController@store')->name('subscriptions.products.store');
    Route::put('/subscriptions/products/{id}', 'Subscription\SubscriptionProductController@update')->name('subscriptions.products.update');
    Route::delete('/subscriptions/products/{id}', 'Subscription\SubscriptionProductController@destroy')->name('subscriptions.products.destroy');
});
