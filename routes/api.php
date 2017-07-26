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
    Route::middleware('web')->get('menu', 'AuthController@menu');
    Route::middleware('web')->get('user/info', 'AuthController@user');
});

// web user
Route::group(['prefix' => 'web/user', 'namespace' => 'Web'], function () {
    Route::get('init', 'UserController@init');
    Route::post('insert', 'UserController@insert');
    Route::put('update', 'UserController@update');
    Route::delete('delete/{user_id}', 'UserController@delete');
    Route::get('search/{str?}', 'UserController@search');
});

// file upload
Route::group(['prefix' => 'file', 'namespace' => 'Web'], function () {
    Route::post('upload/{store_type}', 'FileController@uploadFile');
    Route::get('download/{token}/{file_id}/{user_id}', 'FileController@downloadFile');
});

// MPE set
Route::group(['prefix' => 'web/mpe', 'namespace' => 'MPE'], function () {
    Route::group(['prefix' => 'qa'], function () {
        Route::group(['prefix' => 'receive'], function () {
            Route::get('list', 'QAReceiveController@getList');
            Route::post('posting', 'QAReceiveController@posting');
        });
        Route::group(['prefix' => 'retained'], function () {
            Route::get('list/{ldate?}', 'QARetainedController@getList');
        });
    });
});

// MPZ set
Route::group(['prefix' => 'web/mpz', 'namespace' => 'MPZ'], function () {
    // pointlog
    Route::group(['prefix' => 'pointlog'], function () {
        Route::get('init', 'CatchlogController@init');
        Route::get('check/{point_no}', 'CatchlogController@check');
        Route::post('save', 'CatchlogController@save');
        Route::get('catch/count/{point_no}/{ldate}', 'CatchlogController@catchCount');
    });
});