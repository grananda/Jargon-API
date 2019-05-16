<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('/translations/node/{id}', 'Translations\TranslationController@index')->name('translations.index');
    Route::post('/translations', 'Translations\TranslationController@store')->name('translations.store');
    Route::put('/translations/{id}', 'Translations\TranslationController@update')->name('translations.update');
    Route::delete('/translations/{id}', 'Translations\TranslationController@destroy')->name('translations.destroy');
});
