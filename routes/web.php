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

Route::group(['prefix' => 'admin'], function () {
	Voyager::routes();
});

Route::get('login', 'AuthController@login')->name('login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/', function () {
	return redirect()->route('playtime_requests.index');
})->name('home');


Route::get('requests', 'PlaytimeRequestsController@index')->name('playtime_requests.index');
Route::get('requests/daily', 'PlaytimeRequestsController@daily')->name('playtime_requests.daily');
Route::get('requests/{playtime_request}', 'PlaytimeRequestsController@show')->name('playtime_requests.show');

