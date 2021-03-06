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

Route::group(['prefix' => 'run', 'namespace' => 'Web'], function () {
    Route::get('test', 'RuntestController@test');
});

Route::group(['prefix' => 'jwt', 'namespace' => 'Web'], function () {
    Route::post('login', 'JwtController@login');
    Route::post('refresh', 'JwtController@refresh');
});

// login
Route::group(['prefix' => 'web', 'namespace' => 'Web'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('password', 'AuthController@password');
    Route::post('nativeLogin', 'AuthController@nativeLogin');
    Route::get('logout', 'AuthController@logout');
    Route::get('commonMenu/{class}', 'AuthController@commonMenu');
    Route::middleware('web')->get('menu', 'AuthController@menu');
    Route::middleware('web')->get('user/info', 'AuthController@user');
    Route::get('checkLogin', 'AuthController@checkLogin');    
});

// react native api
Route::group(['prefix' => 'native', 'namespace' => 'Native'], function () {
    Route::group(['prefix' => 'pad'], function () {
        Route::get('/bundle/download/{app}', 'PadController@download');
        Route::get('/bundle/version/{app}', 'PadController@version');
        Route::post('/bundle/save', 'PadController@save');
        Route::get('/apk/download/{app}', 'PadController@apkDownload');
        Route::post('/apk/save', 'PadController@apkSave');
    });
});

// ProductWarehouse
Route::group(['prefix' => 'productWarehouse', 'namespace' => 'ProductWarehouse'], function () {
    // picking
    Route::group(['prefix' => 'picking', 'middleware' => 'jwt'], function () {
        Route::get('list/{date?}', 'PickingController@getPickingList');
        Route::get('item/{stop}/{date?}', 'PickingController@getPickingItem');
        Route::get('items/{stop}/{date?}', 'PickingController@getPickingItems');
        Route::post('start', 'PickingController@startPicking');
        Route::post('end', 'PickingController@endPicking');
        Route::post('pause', 'PickingController@pausePicking');
        Route::post('pickup', 'PickingController@pickup');
    });
    // shipping
    Route::group(['prefix' => 'shipping', 'middleware' => 'jwt'], function () {
        Route::get('info/{spno}/{date?}', 'ShippingController@getShippingInfo');
        Route::post('pieces', 'ShippingController@savePieces');
    });
    // inventory
    Route::group(['prefix' => 'inventory', 'middleware' => 'jwt'], function () {
        Route::get('list/{date?}', 'InventoryController@getInventoryList');
        Route::get('item/{cyno}', 'InventoryController@getInventoryItem');
        Route::get('finished/{cyno}', 'InventoryController@checkFinished');
        Route::post('save', 'InventoryController@saveInventory');
        Route::post('start', 'InventoryController@startInventory');
        Route::post('pause', 'InventoryController@pauseInventory');
        Route::post('end', 'InventoryController@endInventory');
    });
});
Route::get('productWarehouse/inventory/inventoried/{cyno}', 'ProductWarehouse\InventoryController@inventoried');
Route::get('productWarehouse/inventory/export/{cyno}', 'ProductWarehouse\InventoryController@export');

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
    Route::get('ezgetfile/{file_id}', 'FileController@ezGetFile');
});

// MPB routes
Route::group(['prefix' => 'web/mpb', 'namespace' => 'MPB'], function () {
    // MPB Production
    Route::group(['prefix' => 'prod', 'namespace' => 'Production'], function () {
        Route::get('production/list', 'ProductionController@getProduction');
        Route::post('production/compare', 'ProductionController@compare');
        Route::get('production/member/{sno}/{psno}', 'ProductionController@member');
        Route::get('production/material/{sno}/{psno}', 'ProductionController@material');
        Route::post('production/material/check', 'ProductionController@checkMaterial');
        Route::post('production/working/join', 'ProductionController@joinWorking');
        Route::post('production/working/leave', 'ProductionController@leaveWorking');
        Route::post('production/all/join', 'ProductionController@allJoinWorking');
        Route::post('production/all/leave', 'ProductionController@allLeaveWorking');
        Route::post('production/work/complete', 'ProductionController@workComplete');

        Route::get('prework/list', 'PreworkController@getProduction');
        Route::post('prework/compare', 'PreworkController@compare');
        Route::get('prework/member/{sno}/{psno}', 'PreworkController@member');
        Route::post('prework/working/join', 'PreworkController@joinWorking');
        Route::post('prework/working/leave', 'PreworkController@leaveWorking');
        Route::post('prework/all/join', 'PreworkController@allJoinWorking');
        Route::post('prework/all/leave', 'PreworkController@allLeaveWorking');
        Route::post('prework/work/complete', 'PreworkController@workComplete');

        Route::get('clean/list', 'CleanController@getCleanJob');
        Route::post('clean/compare', 'CleanController@compare');
        Route::get('clean/dept/{deptno}', 'CleanController@dept');
        Route::get('clean/member/{sno}/{deptno}', 'CleanController@member');
        Route::post('clean/working/join', 'CleanController@joinWorking');
        Route::post('clean/working/leave', 'CleanController@leaveWorking');

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
        Route::get('package/member/{sno}/{psno}/{pgno}/{duty}', 'PackageController@member');
        Route::get('package/duty/list/{sno}/{psno}', 'PackageController@duty');
        Route::get('package/material/{sno}/{psno}', 'ProductionController@material');
        Route::post('package/material/check', 'ProductionController@checkMaterial');
        Route::post('package/working/join', 'PackageController@joinWorking');
        Route::post('package/working/leave', 'PackageController@leaveWorking');
        Route::post('package/all/join', 'PackageController@allJoinWorking');
        Route::post('package/all/leave', 'PackageController@allLeaveWorking');
        Route::post('package/work/complete', 'PackageController@workComplete');
        Route::post('package/working/duty/close', 'PackageController@dutyClose');
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
            Route::get('check', 'ReceiveController@check');
            Route::get('check/detail/{no}', 'ReceiveController@checkDetail');
            Route::post('confirm', 'ReceiveController@confirm');
            Route::get('user/{empno}', 'ReceiveController@user');
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
            Route::get('storage/list/{str?}', 'StockController@getStorageList');
            Route::put('storage/change', 'StockController@storageChange');
            Route::get('item/{barcode}', 'StockController@itemInfo');
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
            Route::get('barcode/list/{partno}/{batch}', 'DocumentController@barcodeList');
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