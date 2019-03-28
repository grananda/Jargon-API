<?php

use Illuminate\Support\Facades\Route;

Route::middleware('staff')->group(function () {
    Route::get('/options', 'Option\OptionController@index')->name('options.index');
    Route::post('/options', 'Option\OptionController@store')->name('options.store');
    Route::put('/options/{id}', 'Option\OptionController@update')->name('options.update');
    Route::delete('/options/{id}', 'Option\OptionController@destroy')->name('options.destroy');
});
