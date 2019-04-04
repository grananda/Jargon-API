<?php

use Illuminate\Support\Facades\Route;

Route::post('/register', 'User\UserController@store')->name('users.store');

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/user', 'User\UserController@show')->name('users.show');
    Route::delete('/user/{id}', 'User\UserController@destroy')->name('users.destroy');
});
