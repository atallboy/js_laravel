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

Route::any('/token', function () {
    return csrf_token();
});

Route::get('summary/index','SummaryController@index');
Route::group(["prefix"=>"admin",'middleware'=>"permission"],function (){
    Route::post('save','AdminController@create');
    Route::get('user','UserController@get');
});

Route::any('/logout', 'LoginController@logout');
Route::any('/login', 'LoginController@login');
//Route::any('/login', 'AdminController@login');
Route::any('/info', 'AdminController@info');
Route::any('/vue-element-admin/user/logout', 'AdminController@logout');

Route::any('/userList', 'AdminController@info');

Route::any('/route/data', 'RouteController@index');

Route::prefix('admin')->group(function() {
   Route::get('/', 'AdminController@index');
});

Route::prefix('site')->middleware('permission')->group(function() {
    Route::any('/list', 'SiteController@index');
    Route::any('/edit', 'SiteController@edit');
});

Route::prefix('setting')->middleware('permission')->group(function() {
    Route::any('/index', 'SettingController@index');
    Route::any('/edit', 'SettingController@edit');
});

Route::prefix('privacy')->middleware('permission')->group(function() {
    Route::any('/index', 'SettingController@index');
    Route::any('/edit', 'SettingController@edit');
});

Route::prefix('user')->middleware('permission')->group(function() {
    Route::any('/list', 'UserController@index');
    Route::any('/edit', 'UserController@edit');
    Route::any('/del', 'UserController@del');
    Route::any('/reCreateQrcode', 'UserController@reCreateQrcode');
    Route::any('/tellist', 'UserController@index');
});

Route::prefix('item')->middleware('permission')->group(function() {
    Route::any('/list', 'ItemController@index');
    Route::any('/edit', 'ItemController@edit');
    Route::any('/del', 'ItemController@del');
});

Route::prefix('master')->middleware('permission')->group(function() {
    Route::any('/list', 'MasterController@index');
    Route::any('/edit', 'MasterController@edit');
    Route::any('/del', 'MasterController@del');
});

Route::prefix('order')->middleware('permission')->middleware('permission')->group(function() {
    Route::any('/list', 'OrderController@index');  //订单列表
    Route::any('/edit', 'OrderController@edit');
    Route::any('/del', 'OrderController@del');
    Route::any('/changeOrderMaster', 'OrderController@changeOrderMaster');
    Route::any('/refund', 'OrderController@refundOrder');
});

Route::prefix('jiazhongorder')->middleware('permission')->group(function() {
    Route::any('/jiazhonglist', 'JiaZhongOrderController@index');  //获取加钟订单列表

    Route::any('/jzdel', 'JiaZhongOrderController@del');  //删除加钟订单
});


Route::prefix('suggest')->middleware('permission')->group(function() {
    Route::any('/list', 'SuggestController@index');
    Route::any('/edit', 'SuggestController@edit');
    Route::any('/del', 'SuggestController@del');
});

Route::prefix('banner')->middleware('permission')->group(function() {
    Route::any('/list', 'BannerController@index');
    Route::any('/edit', 'BannerController@edit');
    Route::any('/del', 'BannerController@del');
});

Route::prefix('coupon')->middleware('permission')->group(function() {
    Route::any('/list', 'CouponController@index');
    Route::any('/edit', 'CouponController@edit');
    Route::any('/del', 'CouponController@del');
});

Route::prefix('agent')->middleware('permission')->group(function() {
    Route::any('/list', 'AgentController@index');
    Route::any('/edit', 'AgentController@edit');
    Route::any('/del', 'AgentController@del');
});

Route::prefix('withdrawal')->middleware('permission')->group(function() {
    Route::any('/list', 'WithdrawalController@index');
    Route::any('/edit', 'WithdrawalController@edit');
    Route::any('/del', 'WithdrawalController@del');
});

Route::prefix('settleSolution')->middleware('permission')->group(function() {
    Route::any('/list', 'SettleSolutionController@index');
    Route::any('/edit', 'SettleSolutionController@edit');
    Route::any('/del', 'SettleSolutionController@del');
});

Route::prefix('settleLadder')->group(function() {
    Route::any('/list', 'SettleSolutionLadderController@index');
    Route::any('/edit', 'SettleSolutionLadderController@edit');
    Route::any('/del', 'SettleSolutionLadderController@del');
});

Route::prefix('settleRecord')->middleware('permission')->group(function() {
    Route::any('/list', 'SettleRecordController@index');
    Route::any('/del', 'SettleRecordController@del');
});

Route::prefix('mch')->middleware('permission')->group(function() {
    Route::any('/list', 'MchController@index');
    Route::any('/edit', 'MchController@edit');
    Route::any('/del', 'MchController@del');
});

Route::prefix('subscribe')->middleware('permission')->group(function() {
    Route::any('/list', 'SubscribeMessageController@index');
    Route::any('/edit', 'SubscribeMessageController@edit');
    Route::any('/del', 'SubscribeMessageController@del');
    Route::any('/sendSubscribeTest', 'SubscribeMessageController@sendSubscribeTest');
});

Route::prefix('subscribe')->group(function() {
    Route::any('/getSubscribeEvent', 'SubscribeMessageController@getSubscribeEvent');
    Route::any('/getSubscribeParam', 'SubscribeMessageController@getSubscribeParam');
});

Route::prefix('log')->group(function() {
    Route::any('/cate', 'LogController@cate');
    Route::any('/list', 'LogController@index');
    Route::any('/edit', 'LogController@edit');
    Route::any('/del', 'LogController@del');
});

Route::prefix('role')->middleware('permission')->group(function() {
    Route::any('/list', 'RoleController@index');
    Route::any('/edit', 'RoleController@edit');
    Route::any('/del', 'RoleController@del');
});

Route::prefix('role')->group(function() {
    Route::any('/privilege', 'RoleController@privilege');
});

Route::prefix('admin')->middleware('permission')->group(function() {
    Route::any('/list', 'AdminController@index');
    Route::any('/edit', 'AdminController@edit');
    Route::any('/del', 'AdminController@del');
});

Route::prefix('version')->middleware('permission')->group(function() {
    Route::any('/update', 'UpdateController@updateVersion');
});

Route::prefix('backup')->middleware('permission')->group(function() {
    Route::any('/db', 'BackupController@backupDb');
});

Route::prefix('distributeSetting')->middleware('permission')->group(function() {
    Route::any('/index', 'DistributeSettingController@index');
    Route::any('/edit', 'DistributeSettingController@edit');
    Route::any('/mergeImages', 'DistributeSettingController@mergeImages');
});

Route::prefix('distributeMch')->middleware('permission')->group(function() {
    Route::any('/index', 'DistributeMchController@index');
    Route::any('/edit', 'DistributeMchController@edit');
    Route::any('/approve', 'DistributeMchController@approve');
    Route::any('/del', 'DistributeMchController@del');
});

Route::prefix('eva')->middleware('permission')->group(function() {
    Route::any('/index', 'EvaController@index');
    Route::any('/reply', 'EvaController@reply');
    Route::any('/del', 'EvaController@del');
});

Route::prefix('sms')->middleware('permission')->group(function() {
    Route::any('/index', 'SmsController@index');
    Route::any('/edit', 'SmsController@edit');
    Route::any('/testSendSms', 'SmsController@testSendSms');
});

Route::prefix('distributeQrcode')->middleware('permission')->group(function() {
    Route::any('/batch', 'DistributeQrcodeBatchController@index');
    Route::any('/batchDel', 'DistributeQrcodeBatchController@del');
    Route::any('/download', 'DistributeQrcodeBatchController@download');

    Route::any('/index', 'DistributeQrcodeController@index');
    Route::any('/create', 'DistributeQrcodeController@create');
    Route::any('/del', 'DistributeQrcodeController@del');
});


Route::prefix('city')->middleware('permission')->group(function() {
    Route::any('/index', 'CityController@index');
    Route::any('/edit', 'CityController@edit');
    Route::any('/del', 'CityController@del');
});
