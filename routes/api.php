<?php

use Illuminate\Http\Request;

Route::post('login_montir', 'MontirController@login');
Route::post('register_montir', 'MontirController@store');
Route::post('login_customer', 'CustomerController@logins');
Route::post('register_customer', 'CustomerController@register');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get('montir', "MontirController@index"); //read semua montir
	Route::get('montir/{limit}/{offset}', "MontirController@getAll"); //read dengan limit montir
	// Route::post('montir', 'MontirController@store'); //create montir
	Route::put('montir/{id}', "MontirController@update"); //update montir
	Route::delete('montir/{id}', "MontirController@delete"); //delete montir
});
