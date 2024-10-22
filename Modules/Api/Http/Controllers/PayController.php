<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/18 13:45
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use EasyWeChat\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Services\log\LogServices;
use Modules\Api\Services\OrderServices;
use Yansongda\Pay\Pay;

use Illuminate\Http\Request;
class PayController
{

    function query()
    {
        $p = new \Modules\Common\Models\Pay();
        $p->uniacid = 1;
        $p->queryOrder();
    }



    public $uniacid;
    public $agent_cate;
    public $app;
    public $config;

    public function initConfig()
    {
        $setting = DB::table('setting')->where('uniacid',$this->uniacid)->where('del',1)->first();
        $config = [
            'app_id' => $setting->gzh_appid, // 公众账号appid
            'mch_id' => $setting->mch_id, // 商户号
            'key' => $setting->mch_secret, // API密钥
            'cert_path' => '/www/wwwroot/jsdjys/storage/app/cert/apiclient_cert.pem', // 证书绝对路径
            'key_path' => '/www/wwwroot/jsdjys/storage/app/cert/apiclient_key.pem', // 商户证书绝对路径
            'notify_url' => 'https://'.$_SERVER['HTTP_HOST'].'/pay/notify', // 通知地址
        ];

//        $config['app_id'] = 'wx3a058aa889a4605a';
//        $config['mch_id'] = '1683435350';
//        $config['key'] = '6698fbc17adkkg0cb2b88cf99b3f8cb8';

//        echo "<pre>";print_r($config);echo "<pre>";die;
        // 初始化微信应用，这里使用支付配置
        $this->config = $config;
        $this->app = Factory::payment($config);

    }

    // 支付结果通知处理
    public function payNotify(Request $request)
    {
        $this->uniacid = 1;
        $this->initConfig();

        try {
            $response = $this->app->handlePaidNotify(function ($message, $fail) {


                $log = new LogServices();
                $log->uniacid = 1;
                $log->user_id = 999;
                $log->event = 'payNotify';
                $log->remark = date('Y/m/d H:i:s');
                $log->content = json_encode($message);
                $log->saveLog();


                // 使用通知里的 "return_code"、"result_code" 来判断支付结果
                if ($message['return_code'] === 'SUCCESS') {
                    if ($message['result_code'] === 'SUCCESS') {

                        $pay_log = DB::table('paylog')->where('order_no',$message['out_trade_no'])->where('status',0)->where('del',1)->first();
//        DB::beginTransaction();
//        try {
                        if($pay_log){

                            $u = [
                                'status'=>1,
                                'pay_fee'=>$message['total_fee'],
                                'out_trade_no'=>$message['transaction_id'],
                                'notify_result'=>json_encode($message),
                                'update_at'=>time(),
                            ];
                            DB::table('paylog')->where('id',$pay_log->id)
                                ->update($u);

                            $log = new LogServices();
                            $log->uniacid = 1;
                            $log->user_id = 999;
                            $log->event = 'wechatPaySuccess';
                            $log->remark = date('Y/m/d H:i:s');
                            $log->content = json_encode($u);
                            $log->saveLog();

                            $os = new OrderServices();
                            $os->uniacid = $pay_log->uniacid;
                            $os->orderHavePay($pay_log->cate,$pay_log->remark,md5($pay_log->cate.'123'.$pay_log->remark));


                            Log::info('payNotifyEnd----------------',[]);
                            return Pay::wechat()->success();
//            }
//            DB::commit();
//        }catch (\Exception $e){
//            DB::rollBack();
//            $this->apiError('修改失败！');
                            return true; // 返回 true 表示已经处理成功
                        }
                        else{
                            $log = new LogServices();
                            $log->uniacid = 1;
                            $log->user_id = 999;
                            $log->event = 'wechatPayFail';
                            $log->remark = date('Y/m/d H:i:s');
                            $log->content = json_encode($message);
                            $log->saveLog();
                        }

                    } else {
                        // 用户支付失败
                        // 处理支付失败的业务逻辑
                        // ...

                        return $fail('通信成功，但支付失败：' . $message['err_code_des']);
                    }
                } else {
                    // 通信失败，处理失败的情况
                    return $fail('通信失败，请稍后再通知我');
                }
            });

            return $response;
        } catch (\Exception $e) {
            // 记录错误日志
            Log::error('微信支付通知处理异常: ' . $e->getMessage());

            return 'fail'; // 返回 fail 表示通知处理失败
        }
    }

    function payNotify12()
    {

        $log = new LogServices();
        $log->uniacid = 1;
        $log->user_id = 999;
        $log->event = 'payNotify';
        $log->remark = '2.0.9';
        $log->content = 'PayController';
        $log->saveLog();


        Log::info('payNotifyInit------',[date('Y-m-d H:i:s')]);
        Log::info('1115551v1d');
        Pay::config((new \Modules\Common\Models\Pay())->payConfig());
        $result = json_decode(Pay::wechat()->callback(),true);

        $ciphertext = $result['resource']['ciphertext'];
        $order_no = $result['resource']['ciphertext']['out_trade_no'];
        $out_trade_no = $result['resource']['ciphertext']['transaction_id'];
        DB::table('log')->insert(['content'=>json_encode($result),'cate'=>556,'uniacid'=>1,'user_id'=>1]);
        Log::info('payNotifyStart------',$result);
        $p = DB::table('paylog')->where('order_no',$order_no)->where('status',0)->where('del',1)->first();
//        DB::beginTransaction();
//        try {
            if($p){
                DB::table('paylog')->where('id',$p->id)
                    ->update([
                        'status'=>1,
//                        'pay_fee'=>$ciphertext['amount']['total']/100,
//                        'out_trade_no'=>$out_trade_no,
                        'update_at'=>time(),
//                        'notify_result'=>json_encode($ciphertext)
                    ]);
                //充值
                if($p->cate==1){
                    DB::table('order')->where('id',$p->remark)->update(['status'=>1,'update_at'=>time()]);
                }
                if($p->cate==2){
                    DB::table('order')->where('id',$p->remark)->update(['status'=>1,'update_at'=>time()]);
                }
                Log::info('payNotifyEnd----------------',[]);
//                return Pay::wechat()->success();
//            }
//            DB::commit();
//        }catch (\Exception $e){
//            DB::rollBack();
//            $this->apiError('修改失败！');
        }
    }
}
