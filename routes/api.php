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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
// url need api/


Route::get('file/testproc', 'FileController@g_test');

Route::post('file/upload', 'FileController@uploadFile');
Route::get('file/download/{token}/{file_id}/{user_id}', 'FileController@downloadFile');
