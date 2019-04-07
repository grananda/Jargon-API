<?php

use Illuminate\Support\Facades\Route;

Route::post('/register', 'User\AccountController@register')->name('account.register');

Route::get('activate/{id}/{token}', 'User\AccountController@activate')->name('account.activate');
Route::post('resend-activation', 'User\AccountController@resendActivation')->name('account.activate.resend');

Route::group(['middleware' => 'staff'], function () {
    Route::post('deactivate', 'User\AccountController@deactivate')->name('account.deactivate');
});

Route::middleware('auth:api')->group(function () {
    Route::delete('cancel/{id}', 'User\AccountController@cancel')->name('account.cancel');
});
