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
Route::post('pad/login', 'AuthController@login');
Route::get('pad/logout', 'AuthController@logout');
Route::get('pad/menu', 'AuthController@menu');
Route::get('pad/user', 'AuthController@user');

// web user
Route::get('web/user/init', 'Web\UserController@init');
Route::post('web/user/insert', 'Web\UserController@insert');
Route::post('web/user/update', 'Web\UserController@update');
Route::post('web/user/delete', 'Web\UserController@delete');
Route::get('web/user/search/{str}', 'Web\UserController@search');

// file api
Route::post('file/upload/{store_type}', 'FileController@uploadFile');
Route::get('file/download/{token}/{file_id}/{user_id}', 'FileController@downloadFile');
