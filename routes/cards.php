<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('/cards', 'Subscription\CardController@index')->name('cards.index');
    Route::post('/cards', 'Subscription\CardController@store')->name('cards.store');
    Route::put('/cards/{id}', 'Subscription\CardController@update')->name('cards.update');
    Route::delete('/cards/{id}', 'Subscription\CardController@destroy')->name('cards.destroy');
});
