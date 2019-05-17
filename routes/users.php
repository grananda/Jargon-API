<?php

use Illuminate\Support\Facades\Route;

Route::post('/users', 'User\UserController@store')->name('users.store');

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/users', 'User\UserController@show')->name('users.show');
    Route::put('/users/{id}', 'User\UserController@update')->name('users.update');
    Route::delete('/users/{id}', 'User\UserController@destroy')->name('users.destroy');
});
