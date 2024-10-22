<?php
/**
 * @Project: NuoChe
 * @Author: 以简
 * @Date: 2023/6/17 13:23
 * @Description: 版权所有
 */

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Common\Models\Doer;
use Modules\Common\Models\Invite;
use Modules\Common\Models\Wechat;

class LoginController extends ApiController
{

    function login(Request $request)
    {

        $this->code = $request->input('code');
        $nickName = $request->input('nickName');
        $avatarUrl = $request->input('avatarUrl');
        $share_token = $request->input('share_token');
        $res = $this->micrologin();
        $token = '';
        if($res['code']!=200){return $this->apiError();}

        $i = $request->input('i');

        $user = DB::table('user')
            ->where('uniacid',$i)
            ->where('openid',$res['openid'])
            ->where('del',1)
            ->first();
//            echo "<pre>";print_r($res);echo "<pre>";die;

        $userInfo = [
            'recent_at'=>time(),
            'update_at'=>time()
        ];

        if($nickName)$userInfo['nickName'] = $nickName;
        if($avatarUrl)$userInfo['avatarUrl'] = $avatarUrl;

        if(!$user){
            $token = Doer::createToken();
            $userInfo['uniacid'] = $i;
            $userInfo['token'] = $token;
            $userInfo['unionid'] = '';
            $userInfo['openid'] = $res['openid'];
            $userInfo['session_key'] = $res['session_key'];
            $userInfo['create_at'] = time();
            if(array_key_exists('unionid',$res))$userInfo['unionid'] = $res['unionid'];
            $share_id = 0;
            if($share_token){
                $share_id = DB::table('user')->where('token',$share_token)->where('del',1)->value('id');
                if($share_id){
                    $userInfo['pre_id'] = $share_id;

                }
            }

            $id = DB::table('user')->insertGetId($userInfo);
            if($share_id){
                (new Invite())->invitePrize($id);
            }

        }else{
            if(array_key_exists('unionid',$res)&&!$user->unionid)$userInfo['unionid'] = $res['unionid'];
            DB::table('user')->where('id',$user->id)->update($userInfo);
            $token = $user->token;
            if($user->id==2){
//                $token= '07dbd115b349d9c3ee6d96438d816683';
            }
        }

        return $this->apiSuccess('',$token);
    }


    public $code;
    protected $wxLoginUrl;
    protected $wxAppID;
    protected $wxAppSecret;

    private function getUrlStr(){

        $appId = DB::table('setting')->where('uniacid',1)->value('app_id');
        $this->wxAppID = $appId;
        $this->wxAppSecret =DB::table('setting')->where('uniacid',1)->value('app_secret');
        $wxLoginUrl = "https://api.weixin.qq.com/sns/jscode2session?" .
            "appid=%s&secret=%s&js_code=%s&grant_type=authorization_code";
        $this->wxLoginUrl = sprintf($wxLoginUrl, $this->wxAppID, $this->wxAppSecret, $this->code);
    }

    function micrologin(){
        $this->getUrlStr();
        $doer = new Doer();
        $res = json_decode($doer->curl_get($this->wxLoginUrl),true);
//        echo "<pre>";print_r($res);echo "<pre>";
        if(is_array($res) && array_key_exists('openid',$res)){
            $res['code'] = 200;
        }else{
            $res['code'] = 555;
        }
        return ($res);
    }

}
