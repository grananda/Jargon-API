<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'staff'], function () {
    Route::get('/staff/memo', 'Communication\MemoManagementController@index')->name('memos.staff.index');
    Route::get('/staff/memo/{id}', 'Communication\MemoManagementController@show')->name('memos.staff.show');
    Route::post('/staff/memo', 'Communication\MemoManagementController@store')->name('memos.staff.store');
    Route::put('/staff/memo/{id}', 'Communication\MemoManagementController@update')->name('memos.staff.update');
    Route::delete('/staff/memo/{id}', 'Communication\MemoManagementController@destroy')->name('memos.staff.destroy');
});
