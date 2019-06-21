<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('/repositories/{id}', 'Git\RepositoryController@index')->name('repositories.index');
});
