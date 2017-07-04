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

Route::middleware('web')->get('/user', function (Request $request) { // 20170704: 修正以web為主
    return $request->user();
});

// login
Route::post('pad/login', 'AuthController@login');
Route::get('pad/logout', 'AuthController@logout');
Route::get('pad/user', 'AuthController@getUser');

// file api
Route::post('file/upload/{store_type}', 'FileController@uploadFile');
Route::get('file/download/{token}/{file_id}/{user_id}', 'FileController@downloadFile');
