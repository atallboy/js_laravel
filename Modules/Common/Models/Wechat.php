<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/7/27 22:40
 * @Description: 版权所有
 */

namespace Modules\Common\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Wechat
{
    public $uniacid;
    public $appid;
    public $appsecret;
    public function getGzhAccessToken(){
        $setting  = DB::table('access_token')
            ->where('uniacid',$this->uniacid)
            ->where('app_id',$this->appid)
            ->where('invalid_time','>',time())
            ->orderBy('id','desc')
            ->first();
        if($setting){
            return $setting->access_token;
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
        $url = sprintf($url,$this->appid,$this->appsecret);
        $doer = new Doer();
        $res = json_decode($doer->curl_get($url),true);

//            echo "<pre>";print_r($res);echo "<pre>";die;
        if(array_key_exists('access_token',$res)){
            DB::table('access_token')->insert(['access_token'=>$res['access_token'],'invalid_time'=>time()+$res['expires_in']-100]);
            return $res['access_token'];
        }else{
            return  false;
        }

//            echo "<pre>";print_r($res);echo "<pre>";die;
    }

    public function getFocusUserList($access_token){
        $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$access_token;
        $doer = new Doer();
        $res = json_decode($doer->curl_get($url),true);
//            echo "<pre>";print_r($res);echo "<pre>";die;
        if(array_key_exists('count',$res)&&$res['count']>0){
            foreach ($res['data']['openid'] as $v){
                $q = DB::table('user_gzh')->where('openid',$v)->where('del',1)->first();
                if(!$q){
                    $inc = [
                        'uniacid'=>$this->uniacid,
                        'openid'=>$v,
                        'create_at'=>time(),
                    ];
                    DB::table('user_gzh')->insert($inc);
                    $this->getGzgUnionid($access_token,$v);
                }
                else{
                    if(!$q->unionid){
                        $this->getGzgUnionid($access_token,$v);
                    }
                }
            }
        }
    }

    public function getGzgUnionid($access_token,$openid){

        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $doer = new Doer();
        $res = json_decode($doer->curl_get($url),true);
//            echo "<pre>";print_r($res);echo "<pre>";die;
        if(array_key_exists('subscribe',$res)){
            $inc = [
                'subscribe'=>$res['subscribe'],
                'sex'=>$res['sex'],
            ];
            if($res['subscribe']){
                $inc['subscribe_time'] = $res['subscribe_time'];
                $inc['subscribe_scene'] = $res['subscribe_scene'];
            }
            if(array_key_exists('unionid',$res)){
                $inc['unionid'] = $res['unionid'];
                $uid = DB::table('user')->where('unionid',$res['unionid'])
                    ->where('uniacid',$this->uniacid)
                    ->where('del',1)
                    ->value('id');
                if($uid)$inc['user_id'] = $uid;
            }
            DB::table('user_gzh')->where('openid',$openid)->update($inc);
        }
//            return $res;
    }

    //发送订阅消息
    public function sendSubscribeMessage($access_token,$param){
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
        $doer = new Doer();
        $res = json_decode($doer->curl_post($url,json_encode($param)),true);
//            echo "<pre>";print_r($res);echo "<pre>";die;
        return $res;
    }

    //获取小程序码
    public function getMicrQrcode($access_token,$param){
        $WxACodeUrl = sprintf('https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=%s', $access_token);
//            $param = json_encode(array("scene" => $user_id, "width" => 280, 'page' => 'pages/login/login', "auto_color" => false, "is_hyaline" => true,'line_color'=>['r'=>$qrcode['rgb_r'],'g'=>$qrcode['rgb_g'],'b'=>$qrcode['rgb_b']]));
        $doer = new Doer();
        $qrcodeData = ($doer->curl_post($WxACodeUrl,json_encode($param)));
        $file_name = date('YmdHis') . rand(1000, 9999) . '.png';
        $file_path = public_path('/create/merge/'.$file_name);
//        copy(public_path('/create/base/tm.png'), $file_name);
        file_put_contents($file_path, $qrcodeData);
//        echo "<pre>";print_r($access_token);echo "<pre>";
//        echo "<pre>";print_r($qrcodeData);echo "<pre>";die;
        return '/create/merge/'.$file_name;
    }

    public function getGzhQrcode($access_token,$param){
        $WxACodeUrl = sprintf('https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=%s', $access_token);
//            $param = json_encode(array("scene" => $user_id, "width" => 280, 'page' => 'pages/login/login', "auto_color" => false, "is_hyaline" => true,'line_color'=>['r'=>$qrcode['rgb_r'],'g'=>$qrcode['rgb_g'],'b'=>$qrcode['rgb_b']]));
        $doer = new Doer();
        $qrcodeData = json_decode($doer->curl_post($WxACodeUrl,json_encode($param)),true);

        $ticketUrl = sprintf('https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=%s', urlencode($qrcodeData['ticket']));
//        $data = json_decode($doer->curl_get($ticketUrl),true);

        $file_name = date('YmdHis') . rand(1000, 9999) . '.png';
//        $file_path = public_path('/create/merge/'.$file_name);
//        copy(public_path('/create/base/tm.png'), $file_name);
//        file_put_contents($file_path, $qrcodeData);
//        echo "<pre>";print_r($access_token);echo "<pre>";
//        echo "<pre>";print_r($qrcodeData);echo "<pre>";
//        echo "<pre>";print_r($ticketUrl);echo "<pre>";die;
//        echo "<pre>";print_r($data);echo "<pre>";die;
        return $ticketUrl;
    }


    public function getAppWechatAccessToken($code,$uniacid){
        $setting = DB::table('setting')->where('uniacid',$uniacid)->first();
        $appId = 'wxedf889b65eb05c5a';
        $appSecret = 'adc05b9cb6c56d0720e459be346616ac';

//        {
//            "access_token": "ACCESS_TOKEN",
//  "expires_in": 7200,
//  "refresh_token": "REFRESH_TOKEN",
//  "openid": "OPENID",
//  "scope": "snsapi_userinfo",
//  "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
//}

        $params = [
            'appid' => $appId,
            'secret' => $appSecret,
            'code' => $code,
            'grant_type' => 'authorization_code'
        ];
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?" . http_build_query($params);
        $response = Http::get($url);

        return json_decode($response,true);

    }

    public function getAppWechatUserInfo($access_token,$openid){
        $params = [
            'access_token' => $access_token,
            'openid' => $openid,
        ];
        $url = "https://api.weixin.qq.com/sns/userinfo?" . http_build_query($params);
        $response = Http::get($url);

        return json_decode($response,true);
    }

}
