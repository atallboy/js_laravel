<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/8/15 14:35
 * @Description: 版权所有
 */

namespace Modules\Index\Http\Controllers;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Common\Models\Doer;
use Modules\Common\Models\Invite;

class WechatController
{
    protected $auth_url =  "https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=126#wechat_redirect";
    protected $snsapi_base_auth_url =  "https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base&state=126#wechat_redirect";
    protected $access_token = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';
    protected $userinfo_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN';
    protected $userinfo_url_unionId = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN';

    public $uniacid;

    public function reback(){
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = 'weixin';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            return true;
        }else{
            // return false;
            return true;
        }
    }


    public function gzhLogin(Request $request){
        $token = $request->input('token');
        $uniacid = $request->input('i',1);
        $redirect_url= urlencode('https://'.$_SERVER['HTTP_HOST'].'/wechat/gzhLoginBack?i='.$uniacid);
        $setting = DB::table('setting')->where('uniacid',$uniacid)->where('del',1)->first();
        $wxAppID = $setting->gzh_appid;
        $url = sprintf($this->auth_url, $wxAppID, $redirect_url);
//        echo "<pre>";print_r($wxAppID);echo "<pre>";die;
//        echo "<pre>";print_r($url);echo "<pre>";die;
        return redirect($url);
    }

    public function gzhLoginBack(Request $request){

        $distribute_qrcode = 0;   //0无操作 1未激活分销码 2已激活分销码
        $pre_id = false;
        $is_new_user = 0;
        $data = ['user_id'=>0];

        $uniacid = $request->input('i');
        $this->uniacid = $uniacid;
        $token = $request->input('token');
        $code = $request->input('code');
        $setting = DB::table('setting')->where('uniacid',$uniacid)->where('del',1)->first();
        $appid = $setting->gzh_appid;
        $app_secret = $setting->gzh_appsecret;
//        $appid ='wx6bd58d005a3edbd2';
//        $app_secret ='b0b46bcaeddce95229cf11dc5313e81c';
        $url = sprintf($this->access_token, $appid, $app_secret,$code);
        $doer = new Doer();
        $res = $doer->curl_get($url);
        $result = json_decode($res,true);
//        echo "<pre>";print_r($code);echo "<pre>";
//        echo "<pre>";print_r($result);echo "<pre>";die;
        if(array_key_exists('access_token',$result)){
            $url = sprintf($this->userinfo_url, $result['access_token'], $result['openid']);
            $info = json_decode($doer->curl_get($url),true);
//                    echo "<pre>";print_r($info);echo "<pre>";die;
            if(array_key_exists('openid',$info)){

                $userData['nickName'] =$info['nickname'];
                $userData['avatarUrl'] = $info['headimgurl'];
                $userData['update_at'] = time();

                //先处理share_token
                $share_token = $request->session()->get('share_token');
                if($share_token){
                    if(strlen($share_token)==6){
                        $distribute_mch_id = DB::table('distribute_qrcode')->where('serial_number',$share_token)->where('del',1)->value('distribute_mch_id');
                        $distribute_qrcode = 1;
                        if($distribute_mch_id){
                            $distribute_qrcode = 2;
                            $user_id = DB::table('distribute_mch')->where('id',$distribute_mch_id)->where('del',1)->value('user_id');
                            $pre_id = $user_id;
                        }
                    }else{
                        $pre_id = DB::table('user')->where('token',$share_token)->where('del',1)->value('id');
                    }
                    $request->session()->forget('share_token');
                }

                //存在unionid，查看是否跨平台用户
                if(array_key_exists('unionid',$info)){
                    $data['unionid'] = $info['unionid'];
                    $user = DB::table('user')->where('unionid',$info['unionid'])->where('uniacid',$this->uniacid)->where('del',1)->first();
                    if($user){
                        $token = $user->token;
                        $data['user_id'] = $user->id;
                        DB::table('user')->where('id',$user->id)->update($userData);
                    }else{

                        if($pre_id)$userData['pre_id'] = $pre_id;
                        $userData['uniacid'] = $uniacid;
                        $userData['unionid'] = $info['unionid'];
                        $userData['openid'] = $info['openid'];
                        $userData['session_key'] = '';
                        $userData['withdrawal'] = 0;
                        $userData['create_at'] = time();
                        $token = $userData['token'] = Doer::createToken();
                        $data['user_id'] = DB::table('user')->insertGetId($userData);
                        if($share_token){
                            (new Invite())->invitePrize($data['user_id']);
                        }
                    }
                    $query = DB::table('user_gzh')->where('user_id',$data['user_id'])->where('del',1)->first();
                }
                //没有unionid，只是公众号
                else{
                    $user = DB::table('user')->where('openid',$info['openid'])->where('uniacid',$this->uniacid)->where('del',1)->first();
                    if($user){
                        $token = $user->token;
                        $data['user_id'] = $user->id;
                        DB::table('user')->where('id',$user->id)->update($userData);
                    }
                    else{
                        if($pre_id)$userData['pre_id'] = $pre_id;
                        $userData['uniacid'] = $uniacid;
                        $userData['openid'] = $info['openid'];
                        $userData['unionid'] = '';
                        $userData['session_key'] = '';
                        $userData['withdrawal'] = 0;
                        $userData['create_at'] = time();
                        $token = $userData['token'] = Doer::createToken();
                        $data['user_id'] = DB::table('user')->insertGetId($userData);
                        if($share_token){
                            (new Invite())->invitePrize($data['user_id']);
                        }
                    }
                    $query = DB::table('user_gzh')->where('user_id',$data['user_id'])->where('del',1)->first();
                }

                if($query){
                    DB::table('user_gzh')->where('id',$query->id)->update($data);
                }else{
                    $data['openid'] = $info['openid'];
                    $user_gzh_id = DB::table('user_gzh')->insertGetId($data);
                }


//                $user = DB::table('user')
//                    ->where('token',$token)
//                    ->where('del',1)
//                    ->first();
//                echo "<pre>";print_r($user);echo "<pre>";die;

                //存入缓存
                $request->session()->put('test_home_token_2', '$token');
                $request->session()->put('test_home_token', $token);
                $request->session()->put('home_token', $token);
//                echo "<pre>";print_r($token);echo "<pre>";die;
                $url = '/home/index?i='.$uniacid;
                if($distribute_qrcode==1){
                    $distribute_mch_user = DB::table('distribute_mch')->where('user_id',$data['user_id'])->where('status','<',2)->where('del',1)->first();
                    if(!$distribute_mch_user){
                        $url = '/h5/index.html#/pages/distribute/apply?serial_number='.$share_token;
                    }
                }
//                echo "<pre>";print_r($distribute_qrcode);echo "<pre>";
//                echo "<pre>";print_r($distribute_mch_user);echo "<pre>";
//                echo "<pre>";print_r($url);echo "<pre>";
//                echo "<pre>";print_r($data);echo "<pre>";die;
                return redirect($url);
            }else{
                return '登录失败2';
            }
        }else{
            return '登录失败3';
        }
    }
}
