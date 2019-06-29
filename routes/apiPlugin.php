<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('/plugin/{id}', 'Plugin\ApiPluginProjectController@index')->name('plugin.index');

    Route::post('/plugin/sync', 'Plugin\ApiPluginSyncController@store')->name('plugin.store');
});
