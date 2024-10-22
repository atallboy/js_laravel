<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/10/2 07:38
 * @Description: 版权所有
 */

namespace Modules\Common\Models;

use Illuminate\Support\Facades\DB;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
class overtruePay
{

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
//            'cert_path' => '/www/wwwroot/jsdjys/storage/app/cert/apiclient_cert.pem', // 证书绝对路径
//            'key_path' => '/www/wwwroot/jsdjys/storage/app/cert/apiclient_key.pem', // 商户证书绝对路径
            'key_path' => storage_path("app/cert/".$setting->mch_key_pem.".pem"),
            'cert_path' => storage_path("app/cert/".$setting->mch_cert_pem.".pem"),
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

    public function createOrder($data){
        $this->initConfig();
        $order_no = date('YmdHis').rand(1000,9999);
        $order = [
            'out_trade_no' => $order_no,
            'body' => $data['description'],
            'total_fee' => $data['money']*100, // 订单总金额，单位为分
            'trade_type' => 'JSAPI', // JSAPI、NATIVE、APP等
            'openid' => $data['openid'],
//            'openid' => 'oG5-96Gm1UCMURVfPRwXvs_mIHOQ',
//            'openid' => 'oZLn96bBoCMIX_lr4eCwPJcWJ92g'
            // 其他参数可以添加
        ];

        $result = $this->app->order->unify($order);

        $params = [
            'appId' => $this->config['app_id'],
            'timeStamp' => strval(time()),
            'nonceStr' => $this->createNonceStr(),
            'package' => 'prepay_id=' . $result['prepay_id'],
            'signType' => 'MD5',
        ];

        // 生成签名
        $paySign = $this->generateSign($params, $this->config['key']);


//        $user->openid = 'ovm1X5E2lEaDs5qKfsglD6y9-Omk';

        $inc = [
            'uniacid'=>$this->uniacid,
            'user_id'=>$data['user_id'],
            'cate'=>$data['cate'],
            'fee'=>$data['money'],
            'status'=>0,
            'order_no'=>$order_no,
            'remark'=>$data['remark'],
            'create_at'=>time()
        ];
        $payId = DB::table('paylog')->insertGetId($inc);

        // 返回结果中包含paySign
        return array_merge($params, ['paySign' => $paySign]);
    }

    public function refund($data)
    {
        // 获取配置
        $this->initConfig();

        // 创建支付应用实例
//        $app = $this->app;

//        echo "<pre>";print_r($this->app);echo "<pre>";die;

        // 退款参数
        $transactionId = $data['out_trade_no']; // 确保这是微信支付的交易号
        $refundNo = $data['refund_no']; // 商户退款单号

        $refundData = [
            'out_refund_no' => $refundNo,
            'total_fee' => $data['pay_fee'],
            'refund_fee' => $data['refund_fee'],
            'reason' => '平台退款',
            // 其他可选字段，如notify_url等，根据需要添加
        ];
//        echo "<pre>";print_r($refundData);echo "<pre>";die;
        try {
//            $result = $this->app->refund->byTransactionId($data['out_trade_no'], $refund);
            $result = $this->app->refund->byTransactionId($transactionId, $refundNo, $data['pay_fee']*100,$data['refund_fee']*100);
//            echo "<pre>";print_r($result);echo "<pre>";die;
            if ($result['return_code'] === 'SUCCESS' && $result['result_code'] === 'SUCCESS') {
                // 退款成功处理逻辑
                $r = ['code'=>1,'msg'=>'退款成功','transaction_id'=>$result['transaction_id']];
            } else {
                // 退款失败处理逻辑
                $r = ['code'=>0,'msg'=>$result['err_code_des'],'data'=>$refundData,'all_message'=>$refundData];
            }
            return $r;
        } catch (\Exception $e) {

            // 异常处理逻辑
            return response()->json(['status' => 'error', 'message' => 'Error occurred', 'details' => $e->getMessage()]);
        }
    }

    // 生成签名的方法
    protected function generateSign($params, $key)
    {
        ksort($params);
        $string = urldecode(http_build_query($params));
        $string = $string . "&key={$key}";
        return strtoupper(md5($string));
    }

    protected function createNonceStr($length = 16)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

}
