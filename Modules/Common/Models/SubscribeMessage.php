<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/8/1 09:03
 * @Description: 版权所有
 */

namespace Modules\Common\Models;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\log\LogServices;

class SubscribeMessage
{

    public function getSubscribeData(){
        $dataset = [
            ['label'=>'自定义','name'=>'zidingyi','value'=>''],
            ['label'=>'用户昵称','name'=>'nickName','value'=>''],
            ['label'=>'师傅姓名','name'=>'masterName','value'=>''],
            ['label'=>'师傅电话','name'=>'masterTel','value'=>''],
            ['label'=>'订单号','name'=>'orderNo','value'=>''],
            ['label'=>'商品名称','name'=>'snapName','value'=>''],
            ['label'=>'下单时间','name'=>'orderCreateTime','value'=>date('Y-m-d H:i:s')],
            ['label'=>'下单人姓名','name'=>'orderUserName','value'=>''],
            ['label'=>'订单电话','name'=>'orderTel','value'=>''],
            ['label'=>'订单地址','name'=>'OrderAddress','value'=>''],
            ['label'=>'订单备注','name'=>'orderRemark','value'=>''],
        ];

        return $dataset;
    }

    public $user_id;
    public $uniacid;
    public $order_id;

    public function getSubscribeDataValue($subscribe,$openid=''){

        $user = DB::table('user')->where('id',$this->user_id)->first();
//        if(!$openid)$openid = $user->openid;
        $user_gzh = DB::table('user_gzh')->where('user_id',$this->user_id)->first();
        $order = DB::table('order')->where('id',$this->order_id)->first();
        $master = [];
        if($order){
            $master = DB::table('master')->where('id',$order->master_id)->first();
            $master_user = DB::table('user')->where('id',$master->user_id)->first();
            $master_user_gzh = DB::table('user_gzh')->where('user_id',$master->user_id)->first();
        }
        $order_detail = DB::table('order_detail')->where('order_id',$this->order_id)->first();
        $pay_log = DB::table('paylog')
            ->where('cate',1)
            ->where('remark',$this->order_id)->first();

        $order_no = '';
        if($order){
            if($order->pay_type==2){
                $order_no = date('YmdHis',$order->update_at).'_'.$order->id;
            }else{
                $order_no = $pay_log->order_no;
            }
        }


        $dataset = [
            'nickName'=>['label'=>'用户昵称','name'=>'nickName','value'=>$user?$user->nickName:'用户昵称'],
            'masterName'=>['label'=>'师傅姓名','name'=>'masterName','value'=>$master?$master->name:'师傅姓名'],
            'masterTel'=>['label'=>'师傅电话','name'=>'masterTel','value'=>$master?$master->tel:'010-8888888'],
            'orderNo'=>['label'=>'订单号','name'=>'orderNo','value'=>$order_no?$order_no:'123456789'],
            'snapName'=>['label'=>'商品名称','name'=>'snapName','value'=>$order?$order->snap_name:'商品名称'],
            'orderCreateTime'=>['label'=>'下单时间','name'=>'orderCreateTime','value'=>$order?date('Y/m/d H:i:s',$order->create_at):date('Y/m/d H:i:s')],
            'orderUserName'=>['label'=>'下单人姓名','name'=>'orderUserName','value'=>$order_detail?$order_detail->name:'下单人姓名'],
            'orderTel'=>['label'=>'订单电话','name'=>'orderTel','value'=>$order_detail?$order_detail->tel:'010-8888888'],
            'OrderAddress'=>['label'=>'订单地址','name'=>'OrderAddress','value'=>$order_detail?$order_detail->address:'北京市'],
            'orderRemark'=>['label'=>'订单备注','name'=>'orderRemark','value'=>$order?$order->remark:'订单备注'],
        ];

        $form = json_decode($subscribe->form,true);
        $param_data = [];
//        echo "<pre>";print_r($form);echo "<pre>";
//        echo "<pre>";print_r($param_data);echo "<pre>";
//        echo "<pre>";print_r($dataset);echo "<pre>";
//        echo "<pre>";print_r($subscribe);echo "<pre>";
        foreach ($form as $k=>$v){
            if(array_key_exists('cate',$v)&&array_key_exists('name',$v)){
                if($v['cate']=='zidingyi'){
                    $param_data[$v['name']]['value'] = $v['value'];
                }else{
                    $param_data[$v['name']]['value'] = $dataset[$v['cate']]['value'];
                }
            }

        }
//        echo "<pre>";print_r($param_data);echo "<pre>";die;
        $param = [
            'touser'=>$openid,
            'template_id'=>$subscribe->template_id,
            'data'=>$param_data,
        ];

//        $subscribe = DB::table('subscribe_message')->where('trigger_event',1)->get()->toArray();
//        echo "<pre>";print_r($subscribe);echo "<pre>";die;
        return $param;
    }

    function sendSubscribeMessage($param)
    {

        $setting = DB::table('setting')->where('uniacid',$this->uniacid)->where('del',1)->first();

        $wechat = new Wechat();
        $wechat->uniacid = $setting->uniacid;
        $wechat->appid = $setting->gzh_appid;
        $wechat->appsecret = $setting->gzh_appsecret;
        $access_token = $wechat->getGzhAccessToken();

        $log = new LogServices();

        if($param['touser']){
            $user = DB::table('user')->where('openid',$param['touser'])->first();
            if(!$user)return ['errcode'=>111,'errmsg'=>'该openid对应的用户不存在'];
            $user_gzh = DB::table('user_gzh')->where('user_id',$user->id)->first();
        }else{
            $order = DB::table('order')->where('id',$this->order_id)->first();
            $master = DB::table('master')->where('id',$order->master_id)->first();
            $user = DB::table('user')->where('id',$master->user_id)->first();
            $user_gzh = DB::table('user_gzh')->where('user_id',$user->id)->first();
        }

        if(!$user_gzh){
            $wechat->getFocusUserList($access_token);
            $user_gzh = DB::table('user_gzh')->where('user_id',$user->id)->first();
            if(!$user_gzh){
                $log->uniacid = $user->uniacid;
                $log->user_id = $user->id;
                $log->event = 'sendSubscribeMessage';
                $log->content = json_encode(['res'=>'模板消息接收者未关注公众号']);
                $log->saveLog();
                return ;
            }
            $param['touser'] = $user_gzh->openid;
        }



//        echo "<pre>";print_r($order_no);echo "<pre>";die;


        $res = $wechat->sendSubscribeMessage($access_token,$param);

        $log->uniacid = $user->uniacid;
        $log->user_id = $user->id;
        $log->event = 'sendSubscribeMessage';
        $log->content = json_encode(['res'=>$res,'param'=>$param]);
        $log->saveLog();

//        echo "<pre>";print_r($access_token);echo "<pre>";
//        echo "<pre>";print_r($param);echo "<pre>";
//        echo "<pre>";print_r($res);echo "<pre>";die;
        return ['res'=>$res,'param'=>$param];
    }

    function sendMessage()
    {
        $user = DB::table('user')->where('id',$this->user_id)->first();
        $setting = DB::table('setting')->where('uniacid',$user->uniacid)->where('del',1)->first();
        $user_gzh = DB::table('user_gzh')->where('user_id',$this->user_id)->first();
        $order = DB::table('order')->where('id',$this->order_id)->first();
        $master = DB::table('master')->where('id',$order->master_id)->first();
        $master_user = DB::table('user')->where('id',$master->user_id)->first();
        $master_user_gzh = DB::table('user_gzh')->where('user_id',$master->user_id)->first();
        $order_detail = DB::table('order_detail')->where('order_id',$this->order_id)->first();
        $pay_log = DB::table('paylog')
            ->where('cate',1)
            ->where('remark',$this->order_id)->first();

        if($order->pay_type==2){
            $order_no = date('YmdHis',$order->update_at).'_'.$order->id;
        }else{
            $order_no = $pay_log->order_no;
        }


        $dataset = [
            'nickName'=>['label'=>'用户昵称','name'=>'nickName','value'=>$user->nickName],

            'masterName'=>['label'=>'师傅姓名','name'=>'masterName','value'=>$master->name],
            'masterTel'=>['label'=>'师傅电话','name'=>'masterTel','value'=>$master->tel],

            'orderNo'=>['label'=>'订单号','name'=>'orderNo','value'=>$order_no],
            'snapName'=>['label'=>'商品名称','name'=>'snapName','value'=>$order->snap_name],
            'orderCreateTime'=>['label'=>'下单时间','name'=>'orderCreateTime','value'=>date('Y/m/d H:i:s',$order->create_at)],
//            'service_time'=>['label'=>'服务时间','value'=>date('Y/m/d H:i:s',$order->create_time)],
            'orderTel'=>['label'=>'订单电话','name'=>'orderTel','value'=>$order_detail->tel],
            'OrderAddress'=>['label'=>'订单地址','name'=>'OrderAddress','value'=>$order_detail->address],
            'orderRemark'=>['label'=>'订单备注','name'=>'orderRemark','value'=>$order->remark],
        ];


        $wechat = new Wechat();
        $wechat->uniacid = $setting->uniacid;
        $wechat->appid = $setting->gzh_appid;
        $wechat->appsecret = $setting->gzh_appsecret;
        $access_token = $wechat->getGzhAccessToken();

        $log = new LogServices();

        if(!$master_user_gzh){
            $wechat->getFocusUserList($access_token);
            $master_user_gzh = DB::table('user_gzh')->where('user_id',$master->user_id)->first();
            if(!$master_user_gzh){

                $log->uniacid = $user->uniacid;
                $log->user_id = $user->id;
                $log->event = 'sendSubscribeMessage';
                $log->content = json_encode(['res'=>'模板消息接收者未关注公众号']);
                $log->saveLog();
                return ;
            }
        }


//        echo "<pre>";print_r($order_no);echo "<pre>";die;

        $param = [
            'touser'=>$master_user_gzh->openid,
            'template_id'=>'gYcUO-FmNnlOp2UyOW-x9gmkyO2MP8Za-bhPNRFLugo',
            'data'=>[
                'character_string3'=>['value'=>$order_no],
                'phrase7'=>['value'=>$order->snap_name],
                'thing11'=>['value'=>$order_detail->address],
//                'time10'=>['value'=>date('Y-m-d').' '.$order->service_time],
                'time10'=>['value'=>date('Y-m-d',$order->create_at)],
            ],
//            'miniprogram'=>[
//                'appid'=>$setting->app_id,
//                'pagepath'=>'/pages/order/list?id='.$order->id
//            ]
        ];
                $param = [
                    'touser'=>$master_user_gzh->openid,
                    'template_id'=>'zoHN8Xz4MwDPreALpXxXImDBnEPA6f0JITpS1koPNDc',
                    'data'=>[
                        'first'=>['value'=>'新订单通知'],
                        'thing9.DATA'=>['value'=>$order->snap_name],
                        'remark'=>['value'=>$order_no],
                        'thing2.DATA'=>['value'=>$order->snap_name],
                        'time6.DATA'=>['value'=>$order->snap_name],
                        'thing8.DATA'=>['value'=>$order_detail->address],
//                'time10'=>['value'=>date('Y-m-d').' '.$order->service_time],
                        'time5.DATA'=>['value'=>date('Y-m-d H:i',$order->create_at)],
                    ],
                ];



        $res = $wechat->sendSubscribeMessage($access_token,$param);

        $log->uniacid = $user->uniacid;
        $log->user_id = $user->id;
        $log->event = 'sendSubscribeMessage';
        $log->content = json_encode(['res'=>$res,'param'=>$param]);
        $log->saveLog();

//        echo "<pre>";print_r($access_token);echo "<pre>";
//        echo "<pre>";print_r($param);echo "<pre>";
//        echo "<pre>";print_r($res);echo "<pre>";die;
    }


}
