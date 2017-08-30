<?php

Route::get('/payments', 'PaymentsController@index');
Route::post('/payments', 'PaymentsController@store');
Route::get('/payments/create', 'PaymentsController@create');
Route::get('/payments/{payment}', 'PaymentsController@edit');
Route::patch('/payments/{payment}', 'PaymentsController@update');
Route::delete('/payments/{payment}', 'PaymentsController@destroy');

Route::post('/paid', 'PaidController@store');
