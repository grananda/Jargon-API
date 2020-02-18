<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'staff'], function () {
    Route::get('/staff/memos', 'Communication\MemoManagementController@index')->name('memos.staff.index');
    Route::get('/staff/memos/{id}', 'Communication\MemoManagementController@show')->name('memos.staff.show');
    Route::post('/staff/memos', 'Communication\MemoManagementController@store')->name('memos.staff.store');
    Route::put('/staff/memos/{id}', 'Communication\MemoManagementController@update')->name('memos.staff.update');
    Route::delete('/staff/memos/{id}', 'Communication\MemoManagementController@destroy')->name('memos.staff.destroy');
});
