<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('/teams', 'Team\TeamController@index')->name('teams.index');
    Route::get('/teams/{id}', 'Team\TeamController@show')->name('teams.show');
    Route::post('/teams', 'Team\TeamController@store')->name('teams.store');
    Route::put('/teams/{id}', 'Team\TeamController@update')->name('teams.update');
    Route::delete('/teams/{id}', 'Team\TeamController@destroy')->name('teams.destroy');
});
