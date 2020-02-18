<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('/memos', 'Communication\MemoController@index')->name('memos.index');
    Route::get('/memos/{id}', 'Communication\MemoController@show')->name('memos.show');
    Route::post('/memos', 'Communication\MemoController@store')->name('memos.store');
    Route::put('/memos/{id}', 'Communication\MemoController@update')->name('memos.update');
    Route::delete('/memos/{id}', 'Communication\MemoController@destroy')->name('memos.destroy');
});
