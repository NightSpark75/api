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
Route::group(['prefix' => 'web', 'namespace' => 'Web'], function () {
    Route::post('login', 'AuthController@login');
    Route::get('logout', 'AuthController@logout');
    Route::middleware('auth:web')->get('menu', 'AuthController@menu');
    Route::middleware('auth:web')->get('user', 'AuthController@user');
});

// web user
Route::group([/*'middleware' => 'auth:web', */'prefix' => 'web/user', 'namespace' => 'Web'], function () {
    Route::get('init', 'UserController@init');
    Route::post('insert', 'UserController@insert');
    Route::post('update', 'UserController@update');
    Route::post('delete', 'UserController@delete');
    Route::get('search/{str?}', 'UserController@search');
});

// file api
Route::group(['prefix' => 'file', 'namespace' => 'Web'], function () {
    Route::post('upload/{store_type}', 'FileController@uploadFile');
    Route::get('download/{token}/{file_id}/{user_id}', 'FileController@downloadFile');
});

// MPZ
Route::group(['middleware' => 'web', 'prefix' => 'mpz/pad', 'namespace' => 'MPZ'], function () {
    Route::get('init', 'CatchlogController@init');
});