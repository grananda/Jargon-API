<?php

use Illuminate\Support\Facades\Route;

Route::post('/register', 'UserController@store')->name('users.store');

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/user', 'UserController@show')->name('users.show');
    Route::delete('/user/{id}', 'UserController@destroy')->name('users.destroy');
});
