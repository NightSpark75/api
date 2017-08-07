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
    Route::post('upload/old/{store_type}', 'FileController@uploadOldFile');
    Route::get('download/{token}/{file_id}/{user_id}', 'FileController@downloadFile');
});

// MPB routes
Route::group(['prefix' => 'web/mpb', 'namespace' => 'MPB'], function () {
    // MPB Production
    Route::group(['prefix' => 'prod', 'namespace' => 'Production'], function () {
        Route::get('job', 'WorkOrderController@getJob');
        Route::post('compare', 'WorkOrderController@compare');
        Route::get('member/{sno}/{psno}', 'WorkOrderController@member');
        Route::post('working/join', 'WorkOrderController@joinWorking');
        Route::post('working/leave', 'WorkOrderController@leaveWorking');
        Route::post('all/join', 'WorkOrderController@allJoinWorking');
        Route::post('all/leave', 'WorkOrderController@allLeaveWorking');
        Route::post('work/complete', 'WorkOrderController@workComplete');
    });
});


// MPE routes
Route::group(['prefix' => 'web/mpe', 'namespace' => 'MPE'], function () {
    // MPE QA
    Route::group(['prefix' => 'qa', 'namespace' => 'QA'], function () {
        Route::group(['prefix' => 'receive'], function () {
            Route::get('list', 'ReceiveController@getList');
            Route::post('posting', 'ReceiveController@posting');
        });
        Route::group(['prefix' => 'retained'], function () {
            Route::get('list/{ldate}', 'RetainedController@getList');
        });
        Route::group(['prefix' => 'stock'], function () {
            Route::get('list/{str?}', 'StockController@getStockList');
            Route::put('storage/change', 'StockController@storageChange');
        });
    });

    // MPE QC
    Route::group(['prefix' => 'qc', 'namespace' => 'QC'], function () {
        Route::group(['prefix' => 'receive'], function () {
            Route::get('init', 'ReceiveController@init');
            Route::post('posting', 'ReceiveController@posting');
        });
    });
});

// MPZ routes
Route::group(['prefix' => 'web/mpz', 'namespace' => 'MPZ'], function () {
    // pointlog
    Route::group(['prefix' => 'pointlog'], function () {
        Route::get('init', 'PointlogController@init');
        Route::get('check/{point_no}', 'PointlogController@check');
        // catchlog
        Route::post('save', 'CatchlogController@save');
        Route::get('catch/count/{point_no}/{ldate}', 'CatchlogController@catchCount');
    });
});