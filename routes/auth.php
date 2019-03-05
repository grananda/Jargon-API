<?php

use Illuminate\Support\Facades\Route;

Route::post('login', 'AuthController@login')->name('auth.login');

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('logout', 'AuthController@logout')->name('auth->logout');
});
