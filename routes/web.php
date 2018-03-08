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

Route::get('/', 'PlaytimeController@home')->name('home');


Route::get('requests', 'PlaytimeRequestsController@index')->name('playtime_requests.index');
Route::get('requests/daily', 'PlaytimeRequestsController@daily')->name('playtime_requests.daily');
Route::get('requests/{playtime_request}', 'PlaytimeRequestsController@show')->name('playtime_requests.show');

Route::get('playtime', 'PlaytimeController@show')->name('playtimes.show');

Route::get('charts/treemap/', 'PlaytimeController@treemap')->name('charts.treemap');
Route::get('charts/area/', 'PlaytimeController@area')->name('charts.area');
Route::get('charts/sankey/', 'PlaytimeController@sankey')->name('charts.sankey');


Route::get('api/charts/treemap', 'PlaytimeController@treemapAPI')->name('api.charts.treemap');
Route::get('api/charts/area', 'PlaytimeController@areaAPI')->name('api.charts.area');
Route::get('api/charts/sankey', 'PlaytimeController@treemapAPI')->name('api.charts.sankey');

Route::get('settings', 'UsersController@settings')->name('users.settings');
Route::post('settings', 'UsersController@storeSettings')->name('users.storeSettings');