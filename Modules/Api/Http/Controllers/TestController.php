<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2024/10/1 22:51
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use Modules\Common\Models\overtruePay;

class TestController extends ApiController
{

    protected $app;

//    public function __construct()
//    {
//
//        $wechat = [
//            'app_id' => 'wx3a058aa889a4605a', // 公众账号appid
//            'mch_id' => '1683435350', // 商户号
//            'key' => '6698fbc17adkkg0cb2b88cf99b3f8cb8', // API密钥
//            'cert_path' => '/www/wwwroot/jsdjys/storage/app/cert/apiclient_cert.pem', // 证书绝对路径
//            'key_path' => '/www/wwwroot/jsdjys/storage/app/cert/apiclient_key.pem', // 商户证书绝对路径
//            'notify_url' => 'https://cycysdj.cydj.xyz/notify', // 通知地址
//        ];
//
//        // 初始化微信应用，这里使用支付配置
//        $this->app = Factory::payment($wechat);
//    }


    public function apitest(){

        $overtruePay = new overtruePay();
        $overtruePay->uniacid = $this->uniacid;
        $res = $overtruePay->createOrder();

        return $this->apiSuccess('',$res);

        $wechat = [
            'app_id' => 'wx3a058aa889a4605a', // 公众账号appid
            'mch_id' => '1683435350', // 商户号
            'key' => '6698fbc17adkkg0cb2b88cf99b3f8cb8', // API密钥
            'cert_path' => '/www/wwwroot/jsdjys/storage/app/cert/apiclient_cert.pem', // 证书绝对路径
            'key_path' => '/www/wwwroot/jsdjys/storage/app/cert/apiclient_key.pem', // 商户证书绝对路径
            'notify_url' => 'https://cycysdj.cydj.xyz/notify', // 通知地址
        ];

        $app = Factory::payment($wechat);
        if (!$app) {
            return response()->json(['error' => 'Payment service not initialized.']);
        }

        $order = [
            'out_trade_no' => md5(time().rand(100,999)),
            'body' => '商品描述',
            'total_fee' => 100, // 订单总金额，单位为分
            'trade_type' => 'JSAPI', // JSAPI、NATIVE、APP等
            'openid'=>'oG5-96Gm1UCMURVfPRwXvs_mIHOQ'
            // 其他参数可以添加
        ];

        try {
            $result = $app->order->unify($order);


            return response()->json([
                'appId' => $wechat['app_id'],
                'timeStamp' => strval(time()),
                'nonceStr' => $this->createNonceStr(),
                'package' => 'prepay_id=' . $result['prepay_id'],
                'signType' => 'MD5',
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // 生成随机字符串
    protected function createNonceStr($length = 16)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    public function apitest2(Request $request)
    {
        try {
            // 获取前端传递的必要参数，如 openid（用户的唯一标识）和订单信息
            $openid = 'oG5-96Gm1UCMURVfPRwXvs_mIHOQ';
            $outTradeNo = $request->input('out_trade_no'); // 商户订单号
            $totalFee = $request->input('total_fee'); // 订单金额，单位为分
            $body = $request->input('body', '商品描述'); // 商品描述
            $spbillCreateIp = $request->ip(); // 终端IP
            $notifyUrl = route('wechat.pay.notify'); // 支付结果通知回调地址

            // 生成支付订单
            $result = $this->app->order->unify([
                'body' => $body,
                'out_trade_no' => $outTradeNo,
                'total_fee' => $totalFee,
                'spbill_create_ip' => $spbillCreateIp,
                'notify_url' => $notifyUrl,
                'trade_type' => 'JSAPI', // JSAPI支付
                'openid' => $openid, // 用户唯一标识
            ]);

            if (isset($result['prepay_id'])) {
                // 获取支付配置信息
                $config = $this->app->config;

                // 返回给前端需要的支付参数
                return response()->json([
                    'appId' => $config['app_id'],
                    'timeStamp' => strval(time()),
                    'nonceStr' => $this->createNonceStr(),
                    'package' => 'prepay_id=' . $result['prepay_id'],
                    'signType' => 'MD5',
                ]);
            } else {
                // 处理错误
                return response()->json(['error' => '支付订单生成失败', 'details' => $result], 500);
            }
        } catch (InvalidArgumentException $e) {
            // 记录错误日志
            Log::error('微信支付订单创建失败: ' . $e->getMessage());

            // 返回错误信息给前端
            return response()->json(['error' => '参数错误', 'details' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            // 记录错误日志
            Log::error('微信支付订单创建异常: ' . $e->getMessage());

            // 返回通用错误信息给前端
            return response()->json(['error' => '支付异常', 'details' => '请稍后再试'], 500);
        }
    }

}
