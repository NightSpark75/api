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

// 20170704: 修正以web為主
/*
Route::middleware('web')->get('/user', function (Request $request) { 
    return $request->user();
});
*/

// login
Route::group(['prefix' => 'web'], function () {
    Route::post('login', 'Web\AuthController@login');
    Route::get('logout', 'Web\AuthController@logout');
    Route::middleware('web')->get('menu', 'Web\AuthController@menu');
    Route::middleware('web')->get('user', 'Web\AuthController@user');
});

// web user
Route::group(['middleware' => 'web', 'prefix' => 'web/user'], function () {
    Route::get('init', 'Web\UserController@init');
    Route::post('insert', 'Web\UserController@insert');
    Route::post('update', 'Web\UserController@update');
    Route::post('delete', 'Web\UserController@delete');
    Route::get('search/{str?}', 'Web\UserController@search');
});

// file api
Route::group(['prefix' => 'file'], function () {
    Route::post('upload/{store_type}', 'Web\FileController@uploadFile');
    Route::get('download/{token}/{file_id}/{user_id}', 'Web\FileController@downloadFile');
});

// MPZ
Route::group(['middleware' => 'web', 'prefix' => 'mpz/pad'], function () {
    Route::get('init', 'MPZ\CatchlogController@init');
    Route::post('insert', 'Web\UserController@insert');
    Route::post('update', 'Web\UserController@update');
    Route::post('delete', 'Web\UserController@delete');
    Route::get('search/{str?}', 'Web\UserController@search');
});