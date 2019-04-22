<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::post('/customers', 'Subscription\CustomerController@store')->name('customers.store');
});
