<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::post('/branches/{id}', 'Git\BranchController@store')->name('branches.store');
    Route::delete('/branches/{id}', 'Git\BranchController@destroy')->name('branches.destroy');
});
