<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('/memos', 'Communication\MemoController@index')->name('memos.index');
    Route::get('/memos/{id}', 'Communication\MemoController@show')->name('memos.show');
    Route::post('/memos', 'Communication\MemoController@store')->name('memos.store');
    Route::put('/memos/{id}', 'Communication\MemoController@update')->name('memos.update');
    Route::delete('/memos/{id}', 'Communication\MemoController@destroy')->name('memos.destroy');
});

Route::group(['middleware' => 'staff'], function () {
    Route::get('/staff/memo', 'Communication\MemoManagementController@index')->name('memos.staff.index');
    Route::get('/staff/memo/{id}', 'Communication\MemoManagementController@show')->name('memos.staff.show');
    Route::post('/staff/memo', 'Communication\MemoManagementController@store')->name('memos.staff.store');
    Route::put('/staff/memo/{id}', 'Communication\MemoManagementController@update')->name('memos.staff.update');
    Route::delete('/staff/memo/{id}', 'Communication\MemoManagementController@destroy')->name('memos.staff.destroy');
});
