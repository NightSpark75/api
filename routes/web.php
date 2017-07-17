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

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/error', function () {
    return view('error');
});

Route::get('/phpinfo', function () {
    phpinfo();
});

Route::get('/pdo', function () {
    dd (DB::connection()->getPdo());
});

Route::get('/thanks', function () {
    return view('thanks');
})->name('thanks');

Route::get('/test', 'FileController@test');

// change your existing app route to this:
// we are basically just giving it an optional parameter of "anything"
Route::get('/{path?}', function($path = null){
        return View::make('index');
})->where('path', '.*'); 
//regex to match anything (dots, slashes, letters, numbers, etc)

