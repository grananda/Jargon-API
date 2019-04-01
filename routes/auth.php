<?php

use Illuminate\Support\Facades\Route;

Route::get('activate-user/{id}/{token}', 'AuthController@activate')->name('auth.activate');
Route::post('resend-activation', 'AuthController@resendActivation')->name('auth.activate.resend');

Route::group(['middleware' => 'staff'], function () {
    Route::post('deactivate', 'AuthController@deactivate')->name('auth.deactivate');
});

Route::post('login', 'AuthController@login')->name('auth.login');

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('logout', 'AuthController@logout')->name('auth.logout');
});
