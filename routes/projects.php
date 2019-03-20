<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('/projects', 'Project\ProjectController@index')->name('projects.index');
    Route::get('/projects/{id}', 'Project\ProjectController@show')->name('projects.show');
    Route::post('/projects', 'Project\ProjectController@store')->name('projects.store');
    Route::put('/projects/{id}', 'Project\ProjectController@update')->name('projects.update');
    Route::delete('/projects/{id}', 'Project\ProjectController@destroy')->name('projects.destroy');
});
