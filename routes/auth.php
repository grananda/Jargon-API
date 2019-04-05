<?php

use Illuminate\Support\Facades\Route;

Route::post('login', 'User\AuthController@login')->name('auth.login');

Route::post('/request-password-reset', 'User\AuthController@requestPasswordReset')->name('account.password.request');
Route::post('/request-reset', 'User\AuthController@PasswordReset')->name('account.password.reset');

//Route::post('/request-password-reset', 'User\ForgotPasswordController@sendResetLinkEmail')->name('account.password.request');
//Route::post('/request-reset', 'User\ResetPasswordController@reset')->name('account.password.reset');