<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::any('/test','FirstController@index');
Route::any('/job','FirstController@job');
Route::any('/test','FirstController@test');
Route::any('/set','FirstController@set');
Route::any('/get','FirstController@get');