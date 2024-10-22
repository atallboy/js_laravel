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

Route::get('/', 'IndexController@index');
Route::prefix('home')->group(function() {
    Route::get('/index', 'IndexController@index');
});

Route::prefix('wechat')->group(function() {
    Route::get('/gzhLogin', 'WechatController@gzhLogin');
    Route::get('/gzhLoginBack', 'WechatController@gzhLoginBack');
});

Route::prefix('app')->group(function() {
    Route::get('/download', 'IndexController@download');
});
