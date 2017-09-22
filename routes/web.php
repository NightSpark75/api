<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});

Route::get('/ip', function () {
    return request()->ip();
});

Route::get('/service/{msg}', function ($msg) {
    return 'msg = ' . $msg;
});

Route::get('error', function () {
    return view('error');
});

Route::get('phpinfo', function () {
    phpinfo();
});

Route::get('thanks', function () {
    return view('thanks');
})->name('thanks');

Route::get('/web/file/upload/{store_type}/{file_id}/{user_id}',  function ($store_type, $file_id, $user_id) {
    return view('service.upload')
        ->with('file_id', $file_id)
        ->with('user_id', $user_id)
        ->with('store_type', $store_type);
});

Route::get('/native/pad/bundle/upload', 'Native\PadController@upload');
Route::get('/native/pad/apk/upload', 'Native\PadController@apkUpload');

// ReactJs 須認證的頁面
Route::get('/auth/{path?}', function($path = null){
    return View::make('index');
})->where('path', '.*')->middleware('auth'); 

// ReactJs 一般頁面
Route::get('/{path?}', function($path = null){
    return View::make('index');
})->where('path', '.*'); 
