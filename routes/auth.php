<?php

use Illuminate\Support\Facades\Route;

Route::post('login', 'User\AuthController@login')->name('auth.login');

Route::post('/request-password-reset', 'User\AuthController@requestPasswordReset')->name('account.password.request');
Route::get('/request-reset', 'User\AuthController@resetPassword')->name('account.password.reset');
