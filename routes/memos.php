<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('/memos', 'Communication\MemoController@index')->name('memos.index');
    Route::get('/memos/{id}', 'Communication\MemoController@show')->name('memos.show');
    Route::post('/memos', 'Communication\MemoController@store')->name('memos.store');
    Route::put('/memos/{id}', 'Communication\MemoController@update')->name('memos.update');
    Route::delete('/memos/{id}', 'Communication\MemoController@destroy')->name('memos.destroy');

    Route::put('/memos/recipient/{id}', 'Communication\MemoRecipientController@update')->name('memos.recipient.update');
});

Route::group(['middleware' => 'staff'], function () {
    Route::get('/memos/staff', 'Communication\MemoManagementController@index')->name('memos.staff.index');
    Route::get('/memos/staff/{id}', 'Communication\MemoManagementController@show')->name('memos.staff.show');
    Route::post('/memos/staff', 'Communication\MemoManagementController@store')->name('memos.staff.store');
    Route::put('/memos/staff/{id}', 'Communication\MemoManagementController@update')->name('memos.staff.update');
    Route::delete('/memos/staff/{id}', 'Communication\MemoManagementController@destroy')->name('memos.staff.destroy');
});
