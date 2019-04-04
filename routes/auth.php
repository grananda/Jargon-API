<?php

use Illuminate\Support\Facades\Route;

Route::post('login', 'User\AuthController@login')->name('auth.login');
