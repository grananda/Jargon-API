<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('/organizations', 'Organization\OrganizationApiController@index')->name('organizations.index');
    Route::get('/organizations/{id}', 'Organization\OrganizationApiController@show')->name('organizations.show');
    Route::post('/organizations', 'Organization\OrganizationApiController@store')->name('organizations.store');
    Route::put('/organizations/{id}', 'Organization\OrganizationApiController@update')->name('organizations.update');
    Route::delete('/organizations/{id}', 'Organization\OrganizationApiController@destroy')->name('organizations.destroy');
});
