<?php

Route::redirect('/', '/payments');

Route::get('/login', 'Auth\LoginController@index')->name('login');
Route::get('/login/redirect/{provider}', 'Auth\LoginController@create');
Route::get('/login/callback/{provider}', 'Auth\LoginController@store');

Route::middleware('auth')->group(function () {
    Route::get('/payments', 'PaymentsController@index');
    Route::post('/payments', 'PaymentsController@store');
    Route::get('/payments/create', 'PaymentsController@create');
    Route::get('/payments/{payment}', 'PaymentsController@edit');
    Route::patch('/payments/{payment}', 'PaymentsController@update');
    Route::delete('/payments/{payment}', 'PaymentsController@destroy');

    Route::get('/paid', 'PaidController@index');
    Route::post('/paid', 'PaidController@store');
});
