<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('/organizations', 'Organization\OrganizationApiController@index')->name('api.organizations.index');
    Route::get('/organizations/{id}', 'Organization\OrganizationApiController@show')->name('api.organizations.show');
    Route::post('/organizations', 'Organization\OrganizationApiController@store')->name('api.organizations.store');
    Route::put('/organizations/{id}', 'Organization\OrganizationApiController@update')->name('api.organizations.update');
    Route::delete('/organizations/{id}', 'Organization\OrganizationApiController@destroy')->name('api.organizations.destroy');
});
