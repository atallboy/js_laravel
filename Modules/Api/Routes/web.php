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

Route::group(["prefix"=>"api",'middleware'=>"AdminApiAuth"],function (){

    Route::post('upload','UploadController@upload');
    Route::get('getUserInfo','UserController@get');
    Route::post('login','LoginController@login');

    Route::get('autoRun','TaskController@autoRun');

    Route::get('info','InfoController@settingInfo');
    Route::get('settingInfo','InfoController@settingInfo');
    Route::get('bannerList','InfoController@bannerList');

    Route::post('createRechargeOrder','RechargeOrderController@create');
    Route::post('createOrder','OrderController@create');
    Route::get('order/list','OrderController@index'); //获取订单列表




    Route::get('order/detail','OrderController@detail');
    Route::get('order/orderDo','OrderController@orderDo');
    Route::get('order/jiazhong','OrderController@jiazhong');
    Route::post('updateOrder','OrderController@updateOrder');

    Route::get('master/list','MasterController@index');
    Route::post('master/edit','MasterController@edit');
    Route::get('master/detail','MasterController@detail');
    Route::post('master/del','MasterController@edit');
    Route::post('master/changeOpenStatus','MasterController@changeOpenStatus');
    Route::post('master/collect','MasterController@collect');

    // 我写的获取技师定位的

    Route::post('master/jishiaddress','MasterController@jishiaddress');

    Route::get('eva/list','EvaController@index');
    Route::post('eva/submit','EvaController@submit');

    Route::get('item/list','ItemController@index');
    Route::get('item/detail','ItemController@detail');

    Route::get('address/list','AddressController@index');
    Route::get('address/detail','AddressController@detail');
    Route::post('address/edit','AddressController@edit');
    Route::post('address/del','AddressController@del');

    Route::get('coupon/list','CouponController@index');
    Route::get('coupon/detail','CouponController@detail');
    Route::get('coupon/record','CouponController@record');
    Route::post('coupon/exchange','CouponController@exchange');

    Route::post('agent/register','AgentController@register');
    Route::post('agent/info','AgentController@index');

    Route::get('balance/record','BalanceController@record');
    Route::post('balance/withdrawal','BalanceController@withdrawal');
    Route::get('balance/withdrawalRecord','BalanceController@withdrawalRecord');

    Route::post('suggest/submit','SuggestController@submit');
    Route::get('suggest/list','SuggestController@index');

    Route::post('eva/submit','EvaController@submit');
    Route::get('eva/list','EvaController@index');

    Route::get('settle/list','SettleController@index');
    Route::post('settle/settle','SettleController@settle');

    Route::get('mch/list','MchController@index');
    Route::get('mch/detail','MchController@detail');

    Route::get('distribute/getInviteQrcode','DistributeController@getInviteQrcode');
    Route::get('distribute/getInviteData','DistributeController@getInviteData');
    Route::get('distribute/editDistributeMch','DistributeController@editDistributeMch');
    Route::get('distribute/distributeMchInfo','DistributeController@distributeMchInfo');
    Route::get('distribute/order','DistributeController@order');
    Route::get('distribute/qrcodeBind','DistributeController@qrcodeBind');

    Route::any('/privacy/Axb','PrivacyTelController@Axb');
    Route::any('sms/send','SmsController@send');
    Route::any('sms/verify','SmsController@verify');

    Route::any('city/list','CityController@index');

    Route::get('user/appWechatLogin','AppController@appWechatLogin');


    Route::any('/apitest','TestController@apitest');
});

Route::any('/pay/notify','PayController@payNotify');
//Route::any('/pay/query','PayController@query');





