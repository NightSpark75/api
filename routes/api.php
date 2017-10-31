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
    Route::any('nativeLogin', 'AuthController@nativeLogin');
    Route::get('logout', 'AuthController@logout');
    Route::get('commonMenu/{class}', 'AuthController@commonMenu');
    Route::middleware('web')->get('menu', 'AuthController@menu');
    Route::middleware('web')->get('user/info', 'AuthController@user');
});

// react native api
Route::group(['prefix' => 'native', 'namespace' => 'Native'], function () {
    Route::group(['prefix' => 'pad'], function () {
        Route::get('/bundle/download', 'PadController@download');
        Route::get('/bundle/version', 'PadController@version');
        Route::post('/bundle/save', 'PadController@save');
        Route::get('/apk/download', 'PadController@apkDownload');
        Route::post('/apk/save', 'PadController@apkSave');
    });
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

    Route::post('ez/upload', 'FileController@ezUploadFile');
});

// MPB routes
Route::group(['prefix' => 'web/mpb', 'namespace' => 'MPB'], function () {
    // MPB Production
    Route::group(['prefix' => 'prod', 'namespace' => 'Production'], function () {
        Route::get('production/list', 'ProductionController@getProduction');
        Route::post('production/compare', 'ProductionController@compare');
        Route::get('production/member/{sno}/{psno}', 'ProductionController@member');
        Route::post('production/working/join', 'ProductionController@joinWorking');
        Route::post('production/working/leave', 'ProductionController@leaveWorking');
        Route::post('production/all/join', 'ProductionController@allJoinWorking');
        Route::post('production/all/leave', 'ProductionController@allLeaveWorking');
        Route::post('production/work/complete', 'ProductionController@workComplete');

        Route::get('packing/list', 'PackingController@getPacking');
        Route::post('packing/compare', 'PackingController@compare');
        Route::get('packing/member/{sno}/{psno}', 'PackingController@member');
        Route::post('packing/working/join', 'PackingController@joinWorking');
        Route::post('packing/working/leave', 'PackingController@leaveWorking');
        Route::post('packing/all/join', 'PackingController@allJoinWorking');
        Route::post('packing/all/leave', 'PackingController@allLeaveWorking');
        Route::post('packing/work/complete', 'PackingController@workComplete');

        Route::get('package/list', 'PackageController@getPackage');
        Route::post('package/compare', 'PackageController@compare');
        Route::get('package/member/{sno}/{psno}', 'PackageController@member');
        Route::post('package/working/join', 'PackageController@joinWorking');
        Route::post('package/working/leave', 'PackageController@leaveWorking');
        Route::post('package/all/join', 'PackageController@allJoinWorking');
        Route::post('package/all/leave', 'PackageController@allLeaveWorking');
        Route::post('package/work/complete', 'PackageController@workComplete');
    });
});


// MPE routes
Route::group(['prefix' => 'web/mpe', 'namespace' => 'MPE'], function () {
    // MPE QA
    Route::group(['prefix' => 'qa', 'namespace' => 'QA'], function () {
        Route::group(['prefix' => 'receive'], function () {
            Route::get('list', 'ReceiveController@getList');
            Route::get('detail/{lsa_no}', 'ReceiveController@getDetail');
            Route::post('posting', 'ReceiveController@posting');
        });
        Route::group(['prefix' => 'restore'], function () {
            Route::get('list', 'RestoreController@getList');
            Route::post('posting', 'RestoreController@posting');
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
        Route::group(['prefix' => 'doc'], function () {
            Route::get('info/{search}', 'DocumentController@getInfo');
            Route::post('barcode', 'DocumentController@searchByBarcode');
            Route::post('partno', 'DocumentController@searchByPartno');
            Route::post('batch', 'DocumentController@searchByBatch');
            Route::get('read/{doc}/{partno}/{batch}/{file_id}', 'DocumentController@read');
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
        Route::post('catch/save', 'CatchlogController@save');
        Route::get('catch/init/{point_no}', 'CatchlogController@init');
        // templog
        Route::post('temp/save', 'TemplogController@save');
        Route::get('temp/init/{point_no}', 'TemplogController@init');
        // wetestlog
        Route::post('wetest/save', 'WetestlogController@save');
        Route::get('wetest/init/{point_no}', 'WetestlogController@init');
        // refrilog
        Route::post('refri/save', 'RefrilogController@save');
        Route::get('refri/init/{point_no}', 'RefrilogController@init');
        // pressurelog
        Route::post('pressure/save', 'PressurelogController@save');
        Route::get('pressure/init/{point_no}', 'PressurelogController@init');
    });
});