<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('/nodes/project/{id}', 'Node\NodeController@index')->name('nodes.index');
    Route::post('/nodes', 'Node\NodeController@store')->name('nodes.store');
    Route::put('/nodes/{id}', 'Node\NodeController@update')->name('nodes.update');
    Route::delete('/nodes/{id}', 'Node\NodeController@destroy')->name('nodes.destroy');

//    Route::put('/nodes/copy', 'Node\NodeCopyController')->name('nodes.copy');
});
