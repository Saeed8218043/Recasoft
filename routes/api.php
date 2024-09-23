<?php

use Illuminate\Http\Request;

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::any('postData','DataController@postData');
Route::get('getData','ApiController@getData')->name('getData');
Route::any('sendNotification','CronController@sendNotification');
Route::any('test123','CronController@test123');
Route::any('disableSensor','CronController@disableSensor');
Route::any('checkCompaniesState','CronController@checkCompaniesState');
Route::any('singleSensor','CronController@singleSensor');
Route::any('getTok','DataController@getTok');
