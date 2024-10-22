<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/18 12:26
 * @Description: 版权所有
 */

namespace Modules\Common\Models;

use Illuminate\Support\Facades\DB;
use Modules\Api\Services\OrderServices;
use Yansongda\Pay\Pay as YsdPay;

class Pay
{
    public $uniacid;
    public $agent_cate;
    function payConfig()
    {

        $setting =   DB::table('setting')->where('uniacid',$this->uniacid)->where('del',1)->first();
        $config = [
            'mch_id' => $setting->mch_id,
            'mch_secret_key' => $setting->mch_secret,
            'mp_app_id'=>$setting->gzh_appid,
            'mini_app_id'=>$setting->app_id,
            'mch_secret_cert' => storage_path("app/cert/".$setting->mch_key_pem.".pem"),
            'mch_public_cert_path' => storage_path("app/cert/".$setting->mch_cert_pem.".pem"),
        ];
//        echo "<pre>";print_r($config);echo "<pre>";die;
        return [
            'wechat' => [
                'default' => [
                    'mch_id' => $config['mch_id'],
                    // 选填-v2商户私钥
                    'mch_secret_key_v2' => '',
                    // 必填-v3 商户秘钥
                    // 即 API v3 密钥(32字节，形如md5值)，可在 账户中心->API安全 中设置
                    'mch_secret_key' => $config['mch_secret_key'],
                    // 必填-商户私钥 字符串或路径
                    // 即 API证书 PRIVATE KEY，可在 账户中心->API安全->申请API证书 里获得
                    // 文件名形如：apiclient_key.pem
                    'mch_secret_cert' => $config['mch_secret_cert'],
                    // 必填-商户公钥证书路径
                    // 即 API证书 CERTIFICATE，可在 账户中心->API安全->申请API证书 里获得
                    // 文件名形如：apiclient_cert.pem
                    'mch_public_cert_path' => $config['mch_public_cert_path'],
                    // 必填-微信回调url
                    // 不能有参数，如?号，空格等，否则会无法正确回调
                    'notify_url' => 'https://'.$_SERVER['HTTP_HOST'].'/pay/notify',
                    // 选填-公众号 的 app_id
                    // 可在 mp.weixin.qq.com 设置与开发->基本配置->开发者ID(AppID) 查看
                    'mp_app_id' => $config['mp_app_id'],
                    // 选填-小程序 的 app_id
                    'mini_app_id' => $config['mini_app_id'],
                    // 选填-app 的 app_id
                    'app_id' => '',
                    // 选填-合单 app_id
                    'combine_app_id' => '',
                    // 选填-合单商户号
                    'combine_mch_id' => '',
                    // 选填-服务商模式下，子公众号 的 app_id
                    'sub_mp_app_id' => '',
                    // 选填-服务商模式下，子 app 的 app_id
                    'sub_app_id' => '',
                    // 选填-服务商模式下，子小程序 的 app_id
                    'sub_mini_app_id' => '',
                    // 选填-服务商模式下，子商户id
                    'sub_mch_id' => '',
                    // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SERVICE
                    'mode' => \Yansongda\Pay\Pay::MODE_NORMAL,
                ]
            ],

            'logger' => [
                'enable' => true,
                'file' => storage_path('logs/pay.log'),
                'level' => 'debug', // 建议生产环境等级调整为 info，开发环境为 debug
                'type' => 'daily', // optional, 可选 daily.
                'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
            ],
            'http' => [ // optional
                'timeout' => 5.0,
                'connect_timeout' => 5.0,
            ],
        ];
    }
    public $user_id;
    public $cate;
    public $order_data;
    function createPay()
    {
        $user = DB::table('user')->where('id',$this->user_id)->first();
        $openid = $user->openid;
        if($this->agent_cate=='gzh'){
            $openid = DB::table('user_gzh')->where('user_id',$this->user_id)->where('del',1)->value('openid');
        }
        $order_no = date('YmdHis').rand(1000,9999);

//        $user->openid = 'ovm1X5E2lEaDs5qKfsglD6y9-Omk';

        $inc = [
            'uniacid'=>$user->uniacid,
            'user_id'=>$this->user_id,
            'cate'=>$this->cate,
            'fee'=>$this->order_data['money'],
            'order_no'=>$order_no,
            'remark'=>$this->order_data['remark'],
            'create_at'=>time()
        ];
        $payId = DB::table('paylog')->insertGetId($inc);

//        return ['status'=>1,'data'=>$id];
//        return $inc;
        $fee = intval($inc['fee']*100);
//        $fee = 1;
        $order = [
            'out_trade_no' => $order_no,
            'description' => $this->order_data['description'],
            'amount' => ['total' => $fee, 'currency' => 'CNY',],
            'payer' => ['openid' => $openid]
        ];
//        echo "<pre>";print_r($this->payConfig());echo "<pre>";die;
//        echo "<pre>";print_r($this->agent_cate);echo "<pre>";die
        $config = $this->payConfig();
        $config['notify_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/pay/notify';
        $config['return_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/pay/notify';
        YsdPay::config($config);
        if($this->agent_cate=='micro'){
            $result = YsdPay::wechat()->mini($order);
        }elseif ($this->agent_cate=='gzh'){
            $result = YsdPay::wechat()->mp($order);
        }
//        echo "<pre>";print_r($result);echo "<pre>";die;

        return $result;
    }


    //YsdPay
    function refund($data)
    {
        YsdPay::config($this->payConfig());
        $order = [
            'out_trade_no' => $data['order_no'],
            'out_refund_no' => strval($data['refund_no']),
            'amount' => [
                'refund' => intval($data['refund_fee']*100),
                'total' => intval($data['pay_fee']*100),
                'currency' => 'CNY',
            ],
        ];

        $result = json_decode(YsdPay::wechat()->refund($order),true);

        if(array_key_exists('status',$result)&&($result['status']=='PROCESSING'||$result['status']=='SUCCESS')){
            $r = ['code'=>1,'msg'=>'退款成功','transaction_id'=>$result['transaction_id']];
        }else{
            $r = ['code'=>0,'msg'=>$result['message'],'data'=>$order];
        }
        return $r;
//        echo "<pre>";print_r($result);echo "<pre>";die;
    }

    public function queryPaylogByCircle(){
        return true;
        $start_time = time()-3610;
        $pay_log_list = DB::table('paylog')->where('status',0)->where('create_at','>',$start_time)->get()->toArray();
        foreach ($pay_log_list as $k=>$v){
            $this->queryOrder($v->order_no,$v->id);
//            echo "<pre>";print_r($v->order_no);echo "<pre>";
        }

//        echo "<pre>";print_r($pay_log_list);echo "<pre>";
    }

    function queryOrder($order_no,$paylog_id)
    {
        return true;

//        $this->queryPaylogByCircle();die;
//        $order_no = '202409082040165777';

        $config = $this->payConfig();
        YsdPay::config($config);
        $order = [
            'out_trade_no' => $order_no,
        ];
        $result = json_decode(YsdPay::wechat()->query($order),true);
        if(array_key_exists('trade_state',$result) && $result['trade_state']=='SUCCESS'){
            $dateTime = new \DateTime($result['success_time']);
            $timestamp = $dateTime->getTimestamp();

            $pay_log = DB::table('paylog')->where('id',$paylog_id)->where('order_no',$order_no)->first();
            if($pay_log && $pay_log->status==0){
                DB::table('paylog')->where('id',$pay_log->id)->update([
                    'status'=>1,
//                    'pay_fee'=>$result['amount']['total']/100,
                    'out_trade_no'=>$result['transaction_id'],
                    'update_at'=>$timestamp
                ]);

                $os = new OrderServices();
                $os->uniacid = $pay_log->uniacid;
                $res = $os->orderHavePay($pay_log->cate,$pay_log->remark,md5($pay_log->cate.'123'.$pay_log->remark));

//                echo "<pre>";print_r($res);echo "<pre>";
            }


        }
//        echo "<pre>";print_r($result);echo "<pre>";die;
    }

}
